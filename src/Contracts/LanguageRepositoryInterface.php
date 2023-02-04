<?php

namespace Nwidart\Modules\Language\Contracts;

use Illuminate\Support\Collection;
use Nwidart\Modules\Language\Models\Language;

interface LanguageRepositoryInterface
{
    /**
     * @param bool|null $active
     *
     * @return array<string,string>
     */
    public function getOptions(bool $active = null): array;

    /**
     * @return Collection;
     */
    public function getActiveLanguages(): Collection;

    /**
     * @return Language|null
     */
    public function getDefaultLanguage(): ?Language;
}