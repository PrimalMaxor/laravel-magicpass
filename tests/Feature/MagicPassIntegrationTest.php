<?php

namespace Primalmaxor\MagicPass\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Primalmaxor\MagicPass\Models\MagicLoginToken;
use Primalmaxor\MagicPass\Notifications\MagicCodeNotification;
use Tests\TestCase;

class MagicPassIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_package_routes_are_registered()
    {
        $this->assertTrue(route('magicpass.login.form') !== null);
        $this->assertTrue(route('magicpass.send') !== null);
        $this->assertTrue(route('magicpass.verify') !== null);
        $this->assertTrue(route('magicpass.logout') !== null);
    }

    public function test_can_access_login_form()
    {
        $response = $this->get('/magicpass/login');
        $response->assertStatus(200);
        $response->assertSee('Magic Pass Login');
        $response->assertSee('Enter your email to receive a 4-digit login code');
    }

    public function test_can_send_login_code_to_new_user()
    {
        $response = $this->post('/magicpass/send', [
            'email' => 'newuser@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Login code sent to your email!']);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'email_verified_at' => now()->toDateString()
        ]);

        $this->assertDatabaseHas('magic_login_tokens', [
            'user_id' => \App\Models\User::where('email', 'newuser@example.com')->first()->id
        ]);

        $user = \App\Models\User::where('email', 'newuser@example.com')->first();
        Notification::assertSentTo($user, MagicCodeNotification::class);
    }

    public function test_can_send_login_code_to_existing_user()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        $response = $this->post('/magicpass/send', [
            'email' => 'existing@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Login code sent to your email!']);

        $this->assertDatabaseHas('magic_login_tokens', [
            'user_id' => $user->id
        ]);

        Notification::assertSentTo($user, MagicCodeNotification::class);
    }

    public function test_can_verify_valid_code()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $token = MagicLoginToken::create([
            'user_id' => $user->id,
            'code' => '1234',
            'expires_at' => now()->addMinutes(15)
        ]);

        $response = $this->post('/magicpass/verify', [
            'email' => 'test@example.com',
            'code' => '1234'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Login successful!']);

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseMissing('magic_login_tokens', [
            'id' => $token->id
        ]);
    }

    public function test_invalid_code_returns_error()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $response = $this->post('/magicpass/verify', [
            'email' => 'test@example.com',
            'code' => '9999'
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Invalid or expired code']);
    }

    public function test_expired_code_returns_error()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $token = MagicLoginToken::create([
            'user_id' => $user->id,
            'code' => '1234',
            'expires_at' => now()->subMinutes(1)
        ]);

        $response = $this->post('/magicpass/verify', [
            'email' => 'test@example.com',
            'code' => '1234'
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Invalid or expired code']);
    }

    public function test_nonexistent_user_returns_error()
    {
        $response = $this->post('/magicpass/verify', [
            'email' => 'nonexistent@example.com',
            'code' => '1234'
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'User not found']);
    }

    public function test_can_logout()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/magicpass/logout');

        $response->assertRedirect(route('magicpass.login.form'));
        $this->assertGuest();
    }

    public function test_old_codes_are_deleted_when_sending_new_code()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $oldToken = MagicLoginToken::create([
            'user_id' => $user->id,
            'code' => '1111',
            'expires_at' => now()->addMinutes(15)
        ]);

        $response = $this->post('/magicpass/send', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('magic_login_tokens', [
            'id' => $oldToken->id
        ]);

        $this->assertDatabaseHas('magic_login_tokens', [
            'user_id' => $user->id
        ]);
    }
} 