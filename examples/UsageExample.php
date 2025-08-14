<?php

Route::get('/login', function () {
    return redirect()->route('magicpass.login.form');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return 'Welcome to your dashboard!';
    });
    
    Route::get('/profile', function () {
        $user = auth()->user();
        return "Hello {$user->name}!";
    });
});

if (Auth::check()) {
    $user = Auth::user();
} 