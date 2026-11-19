<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\MarkNotificationsAsReadController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
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

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.store');
});

Route::middleware(['auth', 'not-suspended'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('wallet', WalletController::class)->name('wallet');

    Route::get('deposit', [DepositController::class, 'create'])->name('deposit');
    Route::post('deposit', [DepositController::class, 'store'])->middleware('throttle:10,1')->name('deposit.store');

    Route::get('withdraw', [WithdrawalController::class, 'create'])->name('withdraw');
    Route::post('withdraw', [WithdrawalController::class, 'store'])->middleware('throttle:10,1')->name('withdraw.store');

    Route::get('plans', PlanController::class)->name('plans');

    Route::get('tips', [TipController::class, 'index'])->name('tips');
    Route::get('tips/{tip}', [TipController::class, 'show'])->name('tips.show');

    Route::get('bookings', [BookingController::class, 'index'])->name('bookings');
    Route::post('bookings', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('bookings.store');

    Route::get('settings', [ProfileController::class, 'edit'])->name('settings');
    Route::put('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('settings/password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->name('password.update');

    Route::get('support/messages', [SupportMessageController::class, 'index'])->name('support.messages.index');
    Route::post('support/messages', [SupportMessageController::class, 'store'])->middleware('throttle:30,1')->name('support.messages.store');

    Route::post('notifications/mark-all-read', MarkNotificationsAsReadController::class)->name('notifications.mark-all-read');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
