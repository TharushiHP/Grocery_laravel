<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register DocumentStore as singleton for NoSQL-like functionality
        $this->app->singleton(\App\Services\DocumentStore::class, function ($app) {
            return new \App\Services\DocumentStore();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
