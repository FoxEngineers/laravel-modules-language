<?php

namespace Nwidart\Modules\Language\Services;

use Illuminate\Translation\Translator as LaravelTranslator;

/**
 * Class Translator.
 *
 * @property TranslationLoader $loader
 * @method TranslationLoader getLoader()
 */
class Translator extends LaravelTranslator
{
    /**
     * Retrieve all language lines out the loaded file.
     * Warning: need to register namespace first.
     *
     * @param string $namespace
     * @param string $group
     * @param string $locale
     *
     * @return string|array|null
     */
    public function getLines(string $namespace, string $group, string $locale): array|string|null
    {
        $this->load($namespace, $group, $locale);

        return $this->loaded[$namespace][$group][$locale];
    }
}
