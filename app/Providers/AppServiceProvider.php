<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Alert;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No services to register
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Share unread AI alert count globally
         * Used by Navbar, Sidebar, Notification Bell
         */
        View::composer('*', function ($view) {

            $unreadAlerts = 0;

            if (Auth::check()) {
                $unreadAlerts = Alert::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();
            }

            $view->with('unreadAlerts', $unreadAlerts);
        });
    }
}
