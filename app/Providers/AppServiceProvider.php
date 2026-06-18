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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Blade::anonymousComponentPath(resource_path('views/components/flux'), 'flux');

        \Illuminate\Support\Facades\Route::middleware('web')->group(function () {
            \Illuminate\Support\Facades\Route::get('/admin', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'create'])
                ->middleware(['guest'])
                ->name('login');

            \Illuminate\Support\Facades\Route::post('/admin', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'store'])
                ->middleware(['guest'])
                ->name('login.store');
        });
    }
}
