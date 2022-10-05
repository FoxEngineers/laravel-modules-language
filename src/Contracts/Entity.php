<?php

namespace Nwidart\Modules\Language\Contracts;

interface Entity {

    /**
     * @return int
     */
    public function entityId(): int;

    /**
     * @return string
     */
    public function entityType(): string;
}