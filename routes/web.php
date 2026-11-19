<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:10,1');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::view('wallet', 'coming-soon', [
        'page' => 'Wallet',
        'description' => 'Your full balance breakdown and transaction history.',
    ])->name('wallet');

    Route::view('withdraw', 'coming-soon', [
        'page' => 'Withdraw',
        'description' => 'Request payouts to Cash App, Zelle, PayPal, crypto and more.',
    ])->name('withdraw');

    Route::view('plans', 'coming-soon', [
        'page' => 'Plans',
        'description' => 'Browse and manage the plans available to your account.',
    ])->name('plans');

    Route::view('tips', 'coming-soon', [
        'page' => 'Tips',
        'description' => 'Helpful guides for getting the most out of your wallet.',
    ])->name('tips');

    Route::view('bookings', 'coming-soon', [
        'page' => 'Bookings',
        'description' => 'Schedule and keep track of your upcoming bookings.',
    ])->name('bookings');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
