<?php

namespace Nwidart\Modules\Language\Services;

use Nwidart\Modules\Language\Contracts\TranslationInterface;
use DirectoryIterator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Translation\FileLoader;
use Nwidart\Modules\Language\Models\Phrase;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class TranslationRepository implements TranslationInterface
{
    /**
     * @var Translator - this translator has "database loader".
     */
    private Translator $translator;

    /**
     * @var Translator - this translator has "database loader".
     */
    private Translator $fileTranslator;

    /**
     * @var string - /app/resource/lang.
     */
    private string $appLangPath;

    public function __construct()
    {
        $this->translator = app('translator');
        $this->appLangPath = app('path.lang');
        $this->fileTranslator = self::buildFileTranslator();
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getFileTranslator(): Translator
    {
        return $this->fileTranslator;
    }

    /**
     * @param string[] $filters
     * @return array<string, string>
     */
    protected function getNamespaces($filters = []): array
    {
        // All namespaces from modules (per module will have a provider namespace registered lang files.
        $namespaces = $this->getTranslator()->getLoader()->namespaces();

        // Merge with default.
        /** @var array<string, mixed> $namespaces */
        $namespaces = array_merge([Phrase::CORE_NAMESPACE => $this->appLangPath], $namespaces);

        // Because $translator has database loader, so we need to use $fileTranslator which has file loader,
        // we will copy registered namespaces into $fileTranslator.
        foreach ($namespaces as $namespace => $location) {

            // If using filters, we will check if not what we want, skip it for good performance.
            if (!empty($filters)) {
                if (!in_array($namespace, $filters)) {
                    continue;
                }
            }

            $this->getFileTranslator()->addNamespace($namespace, $location);
        }

        return $namespaces;
    }

    /**
     * @param string $location
     * @return string[]
     */
    protected function getFoldersFromLocation(string $location): array
    {
        $languageFolders = [];

        if (!File::isDirectory($location)) {
            return $languageFolders;
        }

        $iterator = new DirectoryIterator($location);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $languageFolders[] = $fileInfo->getBasename();
            }
        }

        return $languageFolders;
    }

    /**
     * @param string[] $filters
     * @return array<string, mixed>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getDefaultLangList(array $filters = []): array
    {
        $languages = [];

        $namespaces = $this->getNamespaces($filters);

        // Start to read all files in namespace location.
        foreach ($namespaces as $namespace => $location) {
            $languageFolders = $this->getFoldersFromLocation($location);

            if (empty($languageFolders)) {
                continue;
            }

            foreach ($languageFolders as $locale) {
                $localeLocation = $location . DIRECTORY_SEPARATOR . $locale;

                if (!File::isDirectory($localeLocation)) {
                    continue;
                }

                $languageFiles = File::files($localeLocation);

                if (empty($languageFiles)) {
                    continue;
                }

                foreach ($languageFiles as $file) {
                    /**
                     * Describe:
                     * namespace:
                     *    * => app/resource/lang/.
                     *    module_name => modules/module_name/resource/lang
                     * group: name of file, example validation.php => group = validation.
                     * locale: en/vi (any folders inside lang folder).
                     * It will build into namespace + locale + file name => app/resource/lang/en/validation.php
                     * Return all lines in the file.
                     */
                    $group = $file->getFilenameWithoutExtension();
                    $lines = $this->getFileTranslator()->getLines($namespace, $group, $locale);
                    if (null === $lines) {
                        continue;
                    }

                    $langStructure = [$group => $lines];
                    if ($namespace != Phrase::CORE_NAMESPACE) {
                        $langStructure = [$namespace => $langStructure];
                    }
                    $data = $this->convertToSimpleArray($langStructure);
                    $dataMultiLang = [];
                    if (!isset($languages[$namespace][$group])) {
                        foreach ($data as $key => $name) {
                            $dataMultiLang[$key] = [$locale => $name];
                        }
                        $languages[$namespace][$group] = $dataMultiLang;

                        continue;
                    }

                    foreach ($data as $key => $name) {
                        if (!isset($languages[$namespace][$group][$key])) {
                            $languages[$namespace][$group][$key] = [];
                        }
                        $languages[$namespace][$group][$key] = array_merge(
                            $languages[$namespace][$group][$key],
                            [$locale => $name]
                        );
                    }
                }
            }
        }

        return $languages;
    }

    public function convertToSimpleArray(array $arr): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
        $result = [];
        foreach ($iterator as $leafValue) {
            $keys = [];
            foreach (range(0, $iterator->getDepth()) as $depth) {
                $keys[] = $iterator->getSubIterator($depth)->key();
            }
            $result[implode('.', $keys)] = $leafValue;
        }

        return $result;
    }

    public function migrateToDatabase(array $filters = []): bool
    {
        $languages = $this->getDefaultLangList($filters);

        $dataInsert = [];

        if (!empty($languages)) {
            foreach ($languages as $group => $files) {
                $this->cleanUp($group);
                foreach ($files as $lines) {
                    foreach ($lines as $key => $text) {
                        $text = json_encode($text);
                        $dataInsert[] = compact('key', 'group', 'text');
                    }
                }
            }
        }

        if (!empty($dataInsert)) {
            Phrase::query()->insert($dataInsert);
        }

        return Cache::forget('locale');
    }

    public function cleanUp(string $group): bool
    {
        return (bool)Phrase::query()
            ->where('group', '=', $group)
            ->delete();
    }

    /**
     * Build a fresh translator with file loader.
     *
     * @return Translator
     */
    public static function buildFileTranslator(): Translator
    {
        $app = app();

        $loader = new FileLoader($app['files'], $app['path.lang']);

        // When registering the translator component, we'll need to set the default
        // locale as well as the fallback locale. So, we'll grab the application
        // configuration so we can easily get both of these values from there.
        $locale = $app['config']['app.locale'];

        $trans = new Translator($loader, $locale);

        $trans->setFallback($app['config']['app.fallback_locale']);

        return $trans;
    }
}