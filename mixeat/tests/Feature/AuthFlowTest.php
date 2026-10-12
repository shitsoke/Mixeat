<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use WithFaker;

    public function test_user_can_register_and_login(): void
    {
        $email = 'maria+'.uniqid().'@example.com';

        $response = $this->post('/register', [
            'first_name' => 'Maria',
            'last_name' => 'Cruz',
            'email' => $email,
            'phone' => '09171234567',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/profile');
        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        $user = User::query()->where('email', $email)->firstOrFail();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertNull($user->email_verified_at, 'Registration does not require email verification.');

        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $loginResponse->assertRedirect('/profile');
        $this->assertAuthenticatedAs($user);
        $this->get('/checkout')->assertOk();

        $this->get('/profile')
            ->assertSee('Maria Cruz')
            ->assertSee($email);

        $this->get('/profile/edit')
            ->assertOk()
            ->assertSee('Edit Profile');

        $this->put('/profile', [
            'first_name' => 'Alpha',
            'last_name' => 'Omega',
            'email' => $email,
            'phone' => '09179998888',
        ])->assertRedirect('/profile');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Alpha Omega',
            'phone' => '09179998888',
        ]);

        $this->get('/profile')
            ->assertSee('Alpha Omega')
            ->assertSee('09179998888');

        $this->get('/email/verify')->assertNotFound();
    }

    public function test_user_can_upload_a_profile_picture(): void
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'first_name' => 'Profile',
            'last_name' => 'Tester',
            'phone' => '09171234567',
        ]);

        $this->actingAs($user)->put('/profile', [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'profile_picture' => UploadedFile::fake()->createWithContent(
                'profile.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADUlEQVR4nGP4z8AAAAMBAQDJ/pLvAAAAAElFTkSuQmCC')
            ),
        ])->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->profile_picture);
        Storage::disk('public')->assertExists($user->profile_picture);

        $this->get('/profile')
            ->assertOk()
            ->assertSee('storage/'.$user->profile_picture);
    }

    public function test_login_shows_only_configured_social_providers(): void
    {
        config([
            'services.google.client_id' => 'google-client',
            'services.google.client_secret' => 'google-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
            'services.facebook.client_id' => 'facebook-client',
            'services.facebook.client_secret' => 'facebook-secret',
            'services.facebook.redirect' => 'http://localhost/auth/facebook/callback',
        ]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('Continue with Google')
            ->assertSee(route('social.redirect', 'google'), false)
            ->assertSee('Continue with Facebook')
            ->assertSee(route('social.redirect', 'facebook'), false);
    }

    public function test_social_login_requires_provider_credentials(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => null,
            'services.facebook.client_id' => null,
            'services.facebook.client_secret' => null,
            'services.facebook.redirect' => null,
        ]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('Google and Facebook sign-in will be available once configured.')
            ->assertDontSee('Continue with Google');

        $this->get('/auth/google/redirect')->assertServiceUnavailable();
    }

    public function test_customer_can_sign_in_with_google_and_returning_social_user_reuses_account(): void
    {
        $this->configureSocialProviders();
        $socialUser = SocialiteUser::fake([
            'id' => 'google-user-123',
            'name' => 'Jamie Doe',
            'email' => 'jamie@example.com',
            'first_name' => 'Jamie',
            'last_name' => 'Doe',
        ]);
        $provider = \Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->twice()->andReturn($socialUser);
        Socialite::shouldReceive('driver')->twice()->with('google')->andReturn($provider);

        $this->get('/oauth/google/callback')
            ->assertRedirect('/profile');

        $this->assertAuthenticated();
        $user = User::where('email', 'jamie@example.com')->firstOrFail();
        $this->assertSame('Jamie Doe', $user->name);
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-user-123',
        ]);

        $this->post('/logout');
        $this->get('/auth/google/callback')->assertRedirect('/profile');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('social_accounts', 1);
    }

    public function test_social_login_does_not_automatically_link_an_existing_email_account(): void
    {
        $this->configureSocialProviders();
        User::factory()->create(['email' => 'existing@example.com']);
        $provider = \Mockery::mock(SocialiteProvider::class);
        $provider->shouldReceive('user')->once()->andReturn(SocialiteUser::fake([
            'id' => 'facebook-user-456',
            'name' => 'Existing Account',
            'email' => 'existing@example.com',
        ]));
        Socialite::shouldReceive('driver')->once()->with('facebook')->andReturn($provider);

        $this->get('/oauth/facebook/callback')
            ->assertRedirect('/login')
            ->assertSessionHasErrors('social');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('social_accounts', 0);
    }

    private function configureSocialProviders(): void
    {
        config([
            'services.google.client_id' => 'google-client',
            'services.google.client_secret' => 'google-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
            'services.facebook.client_id' => 'facebook-client',
            'services.facebook.client_secret' => 'facebook-secret',
            'services.facebook.redirect' => 'http://localhost/auth/facebook/callback',
        ]);
    }
}
