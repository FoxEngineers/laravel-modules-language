<?php

namespace Nwidart\Modules\Language\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\FileLoader;
use Nwidart\Modules\Language\Models\Phrase;

class TranslationLoader extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        $enableCache = (bool)config('translatable.cache');

        if ($enableCache) {
            return Cache::rememberForever(
                "locale.phrases.{$locale}.{$group}",
                function () use ($group, $locale) {
                    return Phrase::getGroup($group, $locale);
                }
            );
        } else {
            return Phrase::getGroup($group, $locale);
        }
    }
}
