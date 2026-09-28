<?php

namespace App\Providers;

use App\Models\Horaire;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
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
        /*
        |--------------------------------------------------------------------------
        | Forcer HTTPS en production
        |--------------------------------------------------------------------------
        */
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        /*
        |--------------------------------------------------------------------------
        | Horaires disponibles dans toutes les vues
        |--------------------------------------------------------------------------
        */
        View::composer('*', function ($view) {
            $view->with('horaires', Horaire::all());
        });
    }
}