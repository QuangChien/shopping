<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Contracts\Services\CategoryServiceInterface;
use App\Services\CategoryService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Repositories
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        
        // Services
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 