<?php

namespace App\Providers;

use App\Services\Admin\AuthService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AuthService
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService();
        });

        // Add these repositories and services
        $this->app->bind(
            \App\Contracts\Repositories\ProductRepositoryInterface::class,
            \App\Repositories\ProductRepository::class
        );

        $this->app->bind(
            \App\Contracts\Services\ProductServiceInterface::class,
            \App\Services\ProductService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
