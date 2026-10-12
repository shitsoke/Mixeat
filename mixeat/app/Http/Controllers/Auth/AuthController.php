<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('storefront.login', [
            'socialProviders' => collect(['google', 'facebook'])
                ->filter(fn (string $provider) => $this->socialProviderConfigured($provider))
                ->values()
                ->all(),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            /** @var User $user */
            $user = Auth::user();

            // Role-based redirects for Admin / Internal users (Bypasses customer email verification)
            if ($user->role === 'marketing_admin') {
                $request->session()->regenerate();

                return redirect()->route('admin.marketing.catering-bookings');
            }

            if ($user->isMarketing()) {
                $request->session()->regenerate();

                return redirect()->route('admin.products.index');
            }

            if ($user->isSupervisor()) {
                $request->session()->regenerate();

                return redirect()->route('admin.supervisors.index');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('profile'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function redirectToProvider(string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
        abort_unless($this->socialProviderConfigured($provider), 503, ucfirst($provider).' sign-in is not configured.');

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        abort_unless(in_array($provider, ['google', 'facebook'], true), 404);
        abort_unless($this->socialProviderConfigured($provider), 503, ucfirst($provider).' sign-in is not configured.');

        if ($request->filled('error')) {
            return redirect()->route('login')->withErrors([
                'social' => 'Sign-in was cancelled or declined. You can try again or use your email and password.',
            ]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (InvalidStateException|GuzzleException $exception) {
            report($exception);

            return redirect()->route('login')->withErrors([
                'social' => 'We could not verify your sign-in with '.ucfirst($provider).'. Please try again.',
            ]);
        }

        $socialAccount = SocialAccount::query()
            ->with('user')
            ->where('provider', $provider)
            ->where('provider_user_id', (string) $socialUser->getId())
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;
        } else {
            $email = strtolower(trim((string) $socialUser->getEmail()));
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->route('login')->withErrors([
                    'social' => ucfirst($provider).' did not provide an email address. Please use email and password instead.',
                ]);
            }

            if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                return redirect()->route('login')->withErrors([
                    'social' => 'An account already uses this email. Sign in with your email and password first.',
                ]);
            }

            $rawUser = $socialUser->getRaw();
            $fullName = trim((string) ($socialUser->getName() ?: $socialUser->getNickname()));
            if ($fullName === '') {
                $fullName = Str::before($email, '@');
            }

            $givenName = trim((string) ($rawUser['first_name'] ?? ''));
            $familyName = trim((string) ($rawUser['last_name'] ?? ''));
            if ($givenName === '' && $familyName === '') {
                $nameParts = preg_split('/\s+/', $fullName, 2) ?: [];
                $givenName = $nameParts[0] ?? $fullName;
                $familyName = $nameParts[1] ?? '';
            }

            $user = DB::transaction(function () use ($email, $provider, $socialUser, $fullName, $givenName, $familyName) {
                $user = User::create([
                    'first_name' => $givenName,
                    'last_name' => $familyName,
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Str::random(64),
                    'role' => 'customer',
                ]);

                $user->socialAccounts()->create([
                    'provider' => $provider,
                    'provider_user_id' => (string) $socialUser->getId(),
                ]);

                return $user;
            });
        }

        if ($user->is_admin || $user->role !== 'customer') {
            return redirect()->route('login')->withErrors([
                'social' => 'Social sign-in is only available for customer accounts.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('profile'));
    }

    private function socialProviderConfigured(string $provider): bool
    {
        return filled(config("services.{$provider}.client_id"))
            && filled(config("services.{$provider}.client_secret"))
            && filled(config("services.{$provider}.redirect"));
    }

    public function showRegisterForm()
    {
        return view('storefront.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $name = trim($validated['first_name'].' '.$validated['last_name']);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $name,
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        Auth::login($user);

        return redirect()->route('profile');
    }

    public function logout(Request $request)
    {
        // 1. Retrieve current cart from session or cookie prior to session destruction
        $cart = session('cart') ?? json_decode($request->cookie('mixeat_cart'), true);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 2. Re-queue cookie for 30 days (43,200 mins) so cart items persist after logout
        if (! empty($cart)) {
            Cookie::queue('mixeat_cart', json_encode($cart), 43200);
        }

        return redirect()->route('home');
    }

    public function editProfile()
    {
        return view('storefront.profile-edit', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        $oldProfilePicture = $user->profile_picture;
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture')->store('profile-pictures', 'public');
            if ($profilePicture === false) {
                throw new \RuntimeException('The profile picture could not be saved.');
            }

            $validated['profile_picture'] = $profilePicture;
        }

        $user->update([
            ...$validated,
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
        ]);

        if (isset($profilePicture) && $oldProfilePicture) {
            Storage::disk('public')->delete($oldProfilePicture);
        }

        return redirect()->route('profile')->with('profile_status', 'Your profile has been updated.');
    }

    public function showChangePasswordForm()
    {
        /** @var User $user */
        $user = Auth::user();

        // Marketing admins are strictly restricted from changing passwords via web UI
        if ($user->isMarketing()) {
            return redirect()->route('profile')->with('profile_status', 'Marketing admin passwords can only be updated directly in the database.');
        }

        return view('storefront.change-password');
    }

    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // Marketing admins are strictly restricted from updating passwords via web UI
        if ($user->isMarketing()) {
            return redirect()->route('profile')->with('profile_status', 'Marketing admin passwords can only be updated directly in the database.');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile')->with('profile_status', 'Your password has been changed successfully.');
    }
}
