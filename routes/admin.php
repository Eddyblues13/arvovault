<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\BookingStatusController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SupportConversationController;
use App\Http\Controllers\Admin\SupportMessageController;
use App\Http\Controllers\Admin\TipController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\TransactionStatusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserFundController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Loaded by bootstrap/app.php with the "web" middleware group, the "/admin"
| URL prefix and the "admin." route name prefix.
|
*/

Route::middleware('admin.guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('admin')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('users', UserController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('users/{user}/funds', [UserFundController::class, 'store'])->name('users.funds.store');

    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::patch('transactions/{transaction}/status', [TransactionStatusController::class, 'update'])->name('transactions.status.update');

    Route::get('support', [SupportConversationController::class, 'index'])->name('support.index');
    Route::get('support/{user}', [SupportConversationController::class, 'show'])->name('support.show');
    Route::get('support/{user}/messages', [SupportMessageController::class, 'index'])->name('support.messages.index');
    Route::post('support/{user}/messages', [SupportMessageController::class, 'store'])->name('support.messages.store');

    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::patch('bookings/{booking}/status', [BookingStatusController::class, 'update'])->name('bookings.status.update');

    Route::resource('plans', PlanController::class)->except(['show']);
    Route::resource('tips', TipController::class)->except(['show']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
