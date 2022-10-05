<?php

namespace Nwidart\Modules\Language\Traits;

/**
 * Trait EntityTrait.
 *
 * @property string $primaryKey
 */
trait EntityTrait {

    public function entityId(): int
    {
        return $this->{$this->primaryKey};
    }

    public function entityType(): string
    {
        return self::ENTITY_TYPE;
    }
}