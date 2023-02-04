<?php

namespace Nwidart\Modules\Language\Tests\Unit\Services;

use Nwidart\Modules\Language\Contracts\TranslationInterface;
use Nwidart\Modules\Language\Services\TranslationLoader;
use Nwidart\Modules\Language\Services\TranslationRepository;
use Nwidart\Modules\Language\Services\Translator;
use Nwidart\Modules\Language\Tests\TestCase;

class TranslationRepositoryTest extends TestCase
{
    /**
     * @return TranslationInterface
     */
    public function testCreateInstance(): TranslationInterface
    {
        $repository = resolve(TranslationInterface::class);
        $this->assertInstanceOf(TranslationRepository::class, $repository);

        return $repository;
    }

    /**
     * @depends testCreateInstance
     * @param TranslationInterface $repository
     */
    public function testCleanUp($repository)
    {
        $data = $repository->cleanUp('test');

        $this->assertIsBool($data);
    }

    /**
     * @depends testCreateInstance
     * @param TranslationInterface $repository
     * @return TranslationInterface
     */
    public function testBuildFileTranslatorSuccess($repository): TranslationInterface
    {
        $fileTranslator = $repository::buildFileTranslator();

        $this->assertInstanceOf(Translator::class, $fileTranslator);
        $this->assertInstanceOf(TranslationLoader::class, $fileTranslator->getLoader());

        return $repository;
    }

    /**
     * @depends testBuildFileTranslatorSuccess
     * @param TranslationInterface $repository
     * @return TranslationInterface
     */
    public function testGetDefaultLangList($repository): TranslationInterface
    {
        $data = $repository->getDefaultLangList([]);

        $this->assertIsArray($data);

        return $repository;
    }

    /**
     * @depends testBuildFileTranslatorSuccess
     * @param TranslationInterface $repository
     */
    public function testGetDefaultLangListByFilter($repository)
    {
        $data = $repository->getDefaultLangList(['blog']);

        $this->assertIsArray($data);
    }

    /**
     * @depends testBuildFileTranslatorSuccess
     * @param TranslationInterface $repository
     */
    public function testAddNamespaceNotExistLocation($repository)
    {
        $repository->getTranslator()->getLoader()->addNamespace('test_fake', 'test_fake_location');
        $data = $repository->getDefaultLangList([]);

        $this->assertIsArray($data);
    }

    /**
     * @depends testBuildFileTranslatorSuccess
     * @param TranslationInterface $repository
     */
    public function testMigrateToDatabase($repository)
    {
        $data = $repository->migrateToDatabase([]);

        $this->assertIsBool($data);
    }

    /**
     * @depends testBuildFileTranslatorSuccess
     * @param TranslationInterface $repository
     */
    public function testMigrateToDatabaseWithFilters($repository)
    {
        $data = $repository->migrateToDatabase(['blog']);

        $this->assertIsBool($data);
    }

    /**
     * @depends testBuildFileTranslatorSuccess
     * @param TranslationInterface $repository
     */
    public function testConvertToSimpleArray($repository)
    {
        $data = $repository->convertToSimpleArray([
            'test' => [
                '123' => [
                    '456' => 'abc',
                ],
            ],
        ]);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('test.123.456', $data);
        $this->assertEquals('abc', $data['test.123.456']);
    }
}
