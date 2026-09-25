<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('storefront.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', Rule::exists('users', 'email')],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Role-based redirects for Admin / Internal users (Bypasses customer email verification)
            if ($user->isMarketing()) {
                $request->session()->regenerate();
                return redirect()->route('admin.products.index');
            }

            if ($user->isSupervisor()) {
                $request->session()->regenerate();
                return redirect()->route('admin.supervisors.index');
            }

            // Check if customer has verified their email address
            if (! $user->hasVerifiedEmail()) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Please verify your email address before logging in.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('profile'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
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

        $name = trim($validated['first_name'] . ' ' . $validated['last_name']);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $name,
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
        ]);

        // Dispatches email verification notification to customer
        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
        ]);

        $user->update([
            ...$validated,
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
        ]);

        return redirect()->route('profile')->with('profile_status', 'Your profile has been updated.');
    }

    public function showChangePasswordForm()
    {
        $user = Auth::user();

        // Marketing admins are strictly restricted from changing passwords via web UI
        if ($user->isMarketing()) {
            return redirect()->route('profile')->with('profile_status', 'Marketing admin passwords can only be updated directly in the database.');
        }

        return view('storefront.change-password');
    }

    public function updatePassword(Request $request)
    {
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