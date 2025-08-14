<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('magicpass.login.form');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ExampleController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ExampleController::class, 'profile'])->name('profile');
    Route::post('/logout', [ExampleController::class, 'logout'])->name('logout');
}); 