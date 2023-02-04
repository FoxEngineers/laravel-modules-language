<?php

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

if (!function_exists('__p')) {
    /**
     * Translate the given message.
     *
     * @param string $key
     * @param array<string, mixed> $replace
     * @param string|null $locale
     *
     * @return string
     * @throws NotFoundExceptionInterface|ContainerExceptionInterface
     */
    function __p(string $key, array $replace = [], string $locale = null): string
    {
        $phrase = app('translator')->get($key, $replace, $locale);

        return is_string($phrase) ? $phrase : $key;
    }
}