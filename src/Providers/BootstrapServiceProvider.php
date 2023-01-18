<?php

namespace Nwidart\Modules\Language\Providers;

use Illuminate\Support\ServiceProvider;

class BootstrapServiceProvider extends ServiceProvider
{

    /**
     * Booting the package.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'module-language-migrations');
        }
    }

    /**
     * Register the provider.
     */
    public function register(): void
    {
        // Register.
    }

    private function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }
}