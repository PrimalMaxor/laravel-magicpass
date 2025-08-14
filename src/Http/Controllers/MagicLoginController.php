<?php

namespace Primalmaxor\MagicPass\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Primalmaxor\MagicPass\Models\MagicLoginToken;
use Primalmaxor\MagicPass\Notifications\MagicCodeNotification;

class MagicLoginController
{
    /**
     * Send a 4-digit code to the user's email
     */
    public function sendCode(Request $request): mixed
    {
        $request->validate(['email' => 'required|email']);

        $key = 'send_code_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, config('magicpass.rate_limit_per_minute', 5))) {
            return response()->json([
                'message' => 'Too many attempts. Please try again later.'
            ], 429);
        }

        RateLimiter::hit($key);

        try {
            $user = User::where('email', $request->email)->first();

            MagicLoginToken::where('user_id', $user->id)->delete();

            do {
                $code = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            } while (MagicLoginToken::where('code', $code)->exists());

            MagicLoginToken::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(config('magicpass.expiry', 15))
            ]);

            $user->notify(new MagicCodeNotification($code));

            return response()->json(['message' => 'Login code sent to your email!']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send code. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify the 4-digit code sent to the user's email
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:4'
        ]);

        $key = 'verify_code_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, config('magicpass.max_attempts', 3))) {
            return response()->json([
                'message' => 'Too many failed attempts. Please try again later.'
            ], 429);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                RateLimiter::hit($key);

                return response()->json(['message' => 'User not found'], 422);
            }

            $record = MagicLoginToken::where('user_id', $user->id)
                ->where('code', $request->code)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$record) {
                RateLimiter::hit($key);
                
                return response()->json(['message' => 'Invalid or expired code'], 422);
            }

            $record->delete();

            Auth::login($user);

            return response()->json(['message' => 'Login successful!']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Verification failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('magicpass::login');
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('magicpass.login.form');
    }
}
