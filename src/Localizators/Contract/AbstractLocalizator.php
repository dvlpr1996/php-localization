<?php

/**
 * @Package: Php Localization Package
 * @Class  : AbstractLocalizator
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace dvlpr1996\PhpLocalization\Localizators\Contract;

use dvlpr1996\PhpLocalization\Exceptions\File\FileException;
use dvlpr1996\PhpLocalization\Exceptions\Localizator\LocalizatorsException;
use dvlpr1996\PhpLocalization\Localizators\Contract\LocalizatorInterface as Localizator;

abstract class AbstractLocalizator implements Localizator
{
    public abstract function all(string $file): array;
    public abstract function get(string $key, array $data, array $replacement = []): string;

    /**
     * handle replacement param
     *
     * @param array $replacement
     * @param string $data lines of text from language file
     * @return string line of text from language file with replacement params
     *
     */
    private function replacement(array $replacement, string $data): string
    {
        $this->checkReplacementKey($replacement);

        foreach ($replacement as $key => $value) {
            $data = match ($this->detectCase($key)) {
                'pascal' => str_ireplace($key, ucfirst($value ?? $key), $data),
                'upper' => str_ireplace($key, strtoupper($value ?? $key), $data),
                'lower' => str_ireplace($key, strtolower($value ?? $key), $data)
            };
        }

        return $data;
    }

    /**
     * Validation Replacement Param
     *
     * @param array $replacement
     * @throws dvlpr1996\PhpLocalization\Exceptions\Localizator\LocalizatorsException
     * @throws dvlpr1996\PhpLocalization\Exceptions\Localizator\LocalizatorsException
     * return LocalizatorsException if replacement is not valid
     */
    private function checkReplacementKey(array $replacement)
    {
        $key = array_keys($replacement)[0];

        if (!preg_match('/^:[A-Za-z0-9]+\b/', $key))
            throw new LocalizatorsException($key . ' Replacement Parameter Key Is Not Valid');

        if (empty($key))
            throw new LocalizatorsException($key . ' Replacement Parameter Key Can Not Be Empty');
    }

    private function detectCase(string $string): string
    {
        $string = substr($string, 1);

        if (preg_match('/^([A-Z]+)$/', $string)) return 'upper';
        if (preg_match('/^([a-z]+)$/', $string)) return 'lower';
        if (preg_match('/\b[A-Z]+[a-z]+\b/', $string)) return 'pascal';
        if (preg_match('/^([A-Za-z][a-z]*)+$/', $string)) return 'title';
        if (preg_match('/^[a-z]+(-[a-z0-9]+)*$/', $string)) return 'kebab';
        if (preg_match('/^[A-Z]+(_[A-Z0-9]+)*$/', $string)) return 'constant';

        return 'lower';
    }

    /**
     * handle fallBack config
     *
     * @param array $data
     * @throws dvlpr1996\PhpLocalization\Exceptions\File\FileException
     */
    protected function fallBack(array $data)
    {
        if (is_null($data['fallBackLang'])) return null;

        $dir = str_replace($data['defaultLang'], $data['fallBackLang'], $data['file']);

        return checkFile($dir) ? realpath($dir) : throw new FileException($dir);
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
