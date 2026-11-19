<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\SupportMessage;
use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->shareLayoutData();
    }

    /**
     * Give the layouts the counts and lists they show on every page.
     */
    private function shareLayoutData(): void
    {
        ViewFactory::composer('components.layouts.app', function (View $view): void {
            $user = Auth::guard('web')->user();

            $view->with([
                'recentNotifications' => $user->notifications()->latest()->limit(6)->get(),
                'unreadNotificationsCount' => $user->unreadNotifications()->count(),
            ]);
        });

        ViewFactory::composer('components.chat-widget', function (View $view): void {
            $view->with(
                'unreadSupportRepliesCount',
                Auth::guard('web')->user()?->supportMessages()->fromAdmin()->unread()->count() ?? 0,
            );
        });

        ViewFactory::composer('components.layouts.admin', function (View $view): void {
            $view->with('navigationCounts', [
                'admin.transactions.index' => Transaction::query()->pending()->count(),
                'admin.support.index' => SupportMessage::query()->fromUser()->unread()->count(),
                'admin.bookings.index' => Booking::query()->pending()->count(),
            ]);
        });
    }
}
