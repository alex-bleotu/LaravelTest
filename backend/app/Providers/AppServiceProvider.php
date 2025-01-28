<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Validator::extend('uppercase', function ($attribute, $value, $parameters, $validator) {
            return strtoupper($value) === $value;
        });

        JsonResource::withoutWrapping();

        $this->loadRoutesFrom(base_path('routes/auth.php'));
    }
}
