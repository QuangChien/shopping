<?php

namespace App\Providers;

use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Services\InventoryServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Repositories\OrderRepository;
use App\Services\InventoryService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        // Register services
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(InventoryServiceInterface::class, InventoryService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
