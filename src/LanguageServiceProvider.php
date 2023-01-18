<?php

namespace Nwidart\Modules\Language;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Language\Contracts\TranslationInterface;
use Nwidart\Modules\Language\Providers\BootstrapServiceProvider;
use Nwidart\Modules\Language\Providers\ConsoleServiceProvider;
use Nwidart\Modules\Language\Services\TranslationLoader;
use Nwidart\Modules\Language\Services\TranslationRepository;
use Nwidart\Modules\Language\Services\Translator;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->registerNamespaces();
        $this->registerModules();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerProviders();
        $this->registerLoader();

        $this->app->extend('translator', function ($service, $app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $service = new Translator($loader, $locale);

            $service->setFallback($app['config']['app.fallback_locale']);

            return $service;
        });

        $this->app->bind(TranslationInterface::class, TranslationRepository::class);
    }

    protected function registerLoader()
    {
        $this->app->extend('translation.loader', function ($service, $app) {
            $service = new TranslationLoader($app['files'], $app['path.lang']);

            return $service;
        });
    }

    /**
     * Bootstrap.
     */
    protected function registerModules()
    {
        $this->app->register(BootstrapServiceProvider::class);
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'modules-language');
        $this->publishes([
            $configPath => config_path('modules-language.php'),
        ], 'config');
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
    }
}