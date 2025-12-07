<?php

namespace LaravelTinyPrint;

use Illuminate\Support\ServiceProvider;
use LaravelTinyPrint\TinyPrint;
use LaravelTinyPrint\Facades\TinyP;

class TinyPrintServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge la config par défaut du package
        $this->mergeConfigFrom(__DIR__.'/../config/tinyp.php', 'tinyp');

        // Singleton de la classe principale
        $this->app->singleton(TinyPrint::class, function ($app) {
            return new TinyPrint($app['config']->get('tinyp', []));
        });

        // Alias de facade (optionnel mais pratique)
        $this->app->alias(TinyPrint::class, 'tinyprint');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publication de la config (commande : php artisan vendor:publish --tag=tinyp-config)
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tinyp.php' => config_path('tinyp.php'),
            ], 'tinyp-config');

            // Optionnel : publier des vues Blade exemples (tickets, étiquettes, etc.)
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/tinyprint'),
            ], 'tinyp-views');

            // Optionnel : publier des assets publics (logos, fonts, QR codes…)
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/tinyprint'),
            ], 'tinyp-assets');
        }

        // Chargement des vues du package (pour pouvoir faire view('tinyprint::ticket'))
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tinyprint');
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [TinyPrint::class, 'tinyprint'];
    }
}
?>