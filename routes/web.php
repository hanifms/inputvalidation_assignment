<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Auth\TwoFactorChallengeController;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('/todo', TodoController::class)->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::post('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorController::class, 'enableTwoFactor'])
        ->name('two-factor.enable');

    Route::delete('/user/two-factor-authentication', [App\Http\Controllers\TwoFactorController::class, 'disableTwoFactor'])
        ->name('two-factor.disable');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/two-factor', [ProfileController::class, 'enableTwoFactor'])->name('profile.two-factor.enable');
    Route::delete('/profile/two-factor', [ProfileController::class, 'disableTwoFactor'])->name('profile.two-factor.disable');
});

// Two-factor authentication routes
Route::middleware(['guest'])->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'create'])
        ->name('2fa.challenge');

    Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store']);
});
