<?php

namespace Nwidart\Modules\Language\Models;

use Illuminate\Database\Eloquent\Model;
use Nwidart\Modules\Language\Contracts\Entity;
use Nwidart\Modules\Language\Traits\EntityTrait;

class Language extends Model implements Entity {

    use EntityTrait;

    public const ENTITY_TYPE = 'language';

    protected $table = 'core_languages';

    /** @var string[] */
    protected $fillable = [
            'language_code',
            'name',
            'charset',
            'version',
            'direction',
            'is_default',
            'is_active',
            'is_master',
            'store_id',
            'updated_at',
            'created_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'is_master' => 'boolean',
    ];

    /**
     * @return LanguageFactory
     */
    protected static function newFactory() {
        return LanguageFactory::new();
    }
}