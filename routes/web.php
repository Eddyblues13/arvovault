<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MarkNotificationsAsReadController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SupportMessageController;
use App\Http\Controllers\TipController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WithdrawalController;
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

    Route::get('wallet', WalletController::class)->name('wallet');

    Route::get('withdraw', [WithdrawalController::class, 'create'])->name('withdraw');
    Route::post('withdraw', [WithdrawalController::class, 'store'])->middleware('throttle:10,1')->name('withdraw.store');

    Route::get('plans', PlanController::class)->name('plans');

    Route::get('tips', [TipController::class, 'index'])->name('tips');
    Route::get('tips/{tip}', [TipController::class, 'show'])->name('tips.show');

    Route::get('bookings', [BookingController::class, 'index'])->name('bookings');
    Route::post('bookings', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('bookings.store');

    Route::get('support/messages', [SupportMessageController::class, 'index'])->name('support.messages.index');
    Route::post('support/messages', [SupportMessageController::class, 'store'])->middleware('throttle:30,1')->name('support.messages.store');

    Route::post('notifications/mark-all-read', MarkNotificationsAsReadController::class)->name('notifications.mark-all-read');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
