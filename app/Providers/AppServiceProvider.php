<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
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
        Paginator::useBootstrapFive();

        // Note: App\Listeners\LogAuthentication is wired to the Login, Failed
        // and Logout events by Laravel's automatic event discovery, which scans
        // app/Listeners and binds every public handle* method to the type-hint
        // of its first parameter. Registering it manually here as well binds
        // each handler twice and writes two audit rows per authentication event.

        View::composer('components.dashboard.sidebar', function ($view) {
            $view->with('productCount', Product::count());
        });
    }
}
