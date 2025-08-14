<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExampleController extends Controller
{
    /**
     * Show the dashboard (protected route)
     */
    public function dashboard()
    {
        return view('dashboard');
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

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        return view('profile', compact('user'));
    }
} 