<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/auth')
                ->group(base_path('routes/auth/auth.php'));

                Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    require base_path('routes/api.php');
                    require base_path('routes/recipes.php');
                });
        });
    }
}
