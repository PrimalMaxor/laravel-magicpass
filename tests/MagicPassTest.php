<?php

namespace Primalmaxor\MagicPass\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Primalmaxor\MagicPass\Models\MagicLoginToken;
use Primalmaxor\MagicPass\Notifications\MagicCodeNotification;
use Tests\TestCase;

class MagicPassTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_can_send_login_code()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $response = $this->post('/magicpass/send', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Login code sent to your email!']);

        $this->assertDatabaseHas('magic_login_tokens', [
            'user_id' => $user->id
        ]);

        Notification::assertSentTo($user, MagicCodeNotification::class);
    }

    public function test_can_verify_login_code()
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

    public function test_can_access_login_form()
    {
        $response = $this->get('/magicpass/login');

        $response->assertStatus(200);
        $response->assertSee('Magic Pass Login');
    }
} 