<?php

/**
 * @Package: Php Localization Package
 * @Class  : AbstractLocalizator
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace PhpLocalization\Localizators\Contract;

use PhpLocalization\Localizators\Contract\LocalizatorInterface as Localizator;

abstract class AbstractLocalizator implements Localizator
{
    public abstract function get(string $key, array $data, array $replacement = []): string;

    public abstract function all(string $file): array;

    protected function replacement(array $replacement, string $data)
    {
        $this->checkReplacement($replacement);

        foreach ($replacement as $key => $value) {
            $data = match ($this->detectCase($key)) {
                'pascal' => str_ireplace($key, ucfirst($value), $data),
                'upper' => str_ireplace($key, strtoupper($value), $data),
                'lower' => str_ireplace($key, strtolower($value), $data)
            };
        }

        return $data;
    }

    protected function checkReplacement(array $replacement)
    {
        $key = array_keys($replacement)[0];
        $value = array_values($replacement)[0];

        if (!is_string($key))
            throw new \Exception($key . 'key replacement parameter not in valid shape');

        if (!is_string($value))
            throw new \Exception($value . ' value replacement parameter should be string');
    }

    protected function detectCase(string $string)
    {
        $string = substr($string, 1);

        if (preg_match('/\b[A-Z0-9]+[a-z0-9]+\b/', $string)) return 'pascal';
        if (preg_match('/\b[A-Z0-9]+\b/', $string)) return 'upper';
        if (preg_match('/\b[a-z0-9]+\b/', $string)) return 'lower';
    }

    protected function fallback(array $data)
    {
        if (is_null($data['fallBackLang'])) return;

        $dir = str_replace($data['defaultLang'], $data['fallBackLang'], $data['file']);

        return checkFile($dir) ? $dir : throw new \Exception($dir . ' Not Exists ');
    }

    protected function getDataByArray(
        string $file,
        string $key,
        array $replacement = [],
        string $fallBack = null
    ): string {

        $fallBackData = isset($fallBack) ? $this->all($fallBack) : null;
        $data = $this->all($file);

        foreach (explode('.', $key) as $segment) {
            if (!isset($data[$segment]))
                $data = $fallBackData;
            $data = $data[$segment] ?? '';
        }

        return (!is_null($replacement) && !empty($replacement))
            ? $this->replacement($replacement, $data)
            : $data;
    }
}
