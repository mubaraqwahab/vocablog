<?php

namespace App\Providers;

use Codewithkyrian\Transformers\Transformers;
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
        Transformers::setup()->setCacheDir(storage_path("app/transformers"))->apply();
    }
}
