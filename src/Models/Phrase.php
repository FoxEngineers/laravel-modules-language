<?php

namespace Nwidart\Modules\Language\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Translatable\HasTranslations;

/**
 * Class Phrase.
 *
 * @property string $key
 * @property string $group
 * @property string $text
 * @mixin Builder
 */
class Phrase extends Model {

    use HasTranslations;

    /** @var string - Laravel namespace in /app/resource/lang. Do not modify this. */
    public const CORE_NAMESPACE = '*';

    /**
     * @var string[]
     */
    protected array $translatable = ['text'];

    /**
     * @param string $group
     * @param string $locale
     *
     * @return array
     */
    public static function getGroup(string $group, string $locale): array {
        return static::query()
                ->where('group', '=', "{$group}")
                ->get()
                ->map(function(self $phrase) use ($locale, $group) {
                    $key = $phrase->key;
                    if ($group !== static::CORE_NAMESPACE) {
                        $key = preg_replace("/{$group}\\./", '', $phrase->key, 1);
                    }

                    $text = $phrase->translate('text', $locale);

                    return compact('key', 'text');
                })
                ->pluck('text', 'key')
                ->toArray();
    }
}