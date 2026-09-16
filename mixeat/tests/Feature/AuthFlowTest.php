<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use WithFaker;

    public function test_user_can_register_and_login(): void
    {
        $email = 'maria+' . uniqid() . '@example.com';

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

        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $loginResponse->assertRedirect('/profile');
        $this->assertAuthenticatedAs($user);

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
    }
}
