<?php

use Illuminate\Support\Facades\Route;
use Primalmaxor\MagicPass\Http\Controllers\MagicLoginController;

Route::middleware('web')->group(function () {
    Route::get('/magicpass/login', [MagicLoginController::class, 'showLoginForm'])->name('magicpass.login.form');
    Route::post('/magicpass/send', [MagicLoginController::class, 'sendCode'])->name('magicpass.send');
    Route::post('/magicpass/verify', [MagicLoginController::class, 'verifyCode'])->name('magicpass.verify');
    Route::post('/magicpass/logout', [MagicLoginController::class, 'logout'])->name('magicpass.logout');
});
