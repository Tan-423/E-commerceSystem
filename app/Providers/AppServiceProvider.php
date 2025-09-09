<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CartInterface;
use App\Adapters\CartAdapter;
use App\Models\Order;
use App\Models\User;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CartInterface::class, CartAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
    }
}
