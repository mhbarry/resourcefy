<?php

namespace Mhbarry\Resourcefy;

use Illuminate\Support\ServiceProvider;
use Laranuxt\Resourcefy\Commands\ResetAppCommand;

class ResourcefyServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laranuxt');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laranuxt');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
//         $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->app['router']->namespace('Laranuxt\\Resourcefy\\Controllers')
            ->middleware(['api'])
                ->prefix('api')
                ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
            });

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/resourcefy.php', 'resourcefy');

        // Register the service the package provides.
        $this->app->singleton('resourcefy', function ($app) {
            return new Resourcefy;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['resourcefy'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/resourcefy.php' => config_path('resourcefy.php'),
        ], 'resourcefy.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/laranuxt'),
        ], 'resourcefy.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/laranuxt'),
        ], 'resourcefy.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/laranuxt'),
        ], 'resourcefy.views');*/

        // Registering package commands.
         $this->commands([ResetAppCommand::class]);
    }
}
