<?php

namespace Nwidart\Modules\Language\Contracts;

use Nwidart\Modules\Language\Services\Translator;

interface TranslationInterface
{
    /**
     * Get Translator.
     *
     * @return Translator
     */
    public function getTranslator(): Translator;

    /**
     * Get File Translator.
     *
     * @return Translator
     */
    public function getFileTranslator(): Translator;

    /**
     * Get default language list.
     *
     * @param string[] $filters - Filter namespaces (using when you only need to check certain modules).
     *
     * @return array<string, mixed>
     */
    public function getDefaultLangList(array $filters = []): array;

    /**
     * @param array<mixed> $arr
     *
     * @description convert ['a' => ['b' => 123]] => ['a.b' => 123]
     * @return array<mixed>
     */
    public function convertToSimpleArray(array $arr): array;

    /**
     * Convert all lang files to database.
     *
     * @param string[] $filters
     *
     * @return bool
     */
    public function migrateToDatabase(array $filters = []): bool;

    /**
     * Clean up all phrases from a group.
     *
     * @param string $group
     *
     * @return bool
     */
    public function cleanUp(string $group): bool;

    /**
     * Build a fresh translator with file loader.
     *
     * @return Translator
     */
    public static function buildFileTranslator(): Translator;
}