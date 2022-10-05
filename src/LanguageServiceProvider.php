<?php

namespace Nwidart\Modules\Language;

use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Language\Contracts\TranslationInterface;
use Nwidart\Modules\Language\Providers\BootstrapServiceProvider;
use Nwidart\Modules\Language\Services\TranslationLoader;
use Nwidart\Modules\Language\Services\TranslationRepository;
use Nwidart\Modules\Language\Services\Translator;

class LanguageServiceProvider extends ServiceProvider {

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
    public function register() {
        $this->registerLoader();

        $this->app->singleton('translator', function($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });

        $this->app->bind(TranslationInterface::class, TranslationRepository::class);
    }

    protected function registerLoader() {
        $this->app->singleton('translation.loader', function($app) {
            return new TranslationLoader($app['files'], $app['path.lang']);
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
}