<?php

/**
 * @Package: Php Localization Package
 * @Class  : ArrayLocalizator
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace PhpLocalization\Localizators;

use PhpLocalization\Localizators\Contract\LocalizatorInterface as Localizator;
use PhpParser\Node\Name;

class ArrayLocalizator implements Localizator
{
    public function get(string $file, string $key, array $replacement = []): string
    {
        $data = $this->all($file);

        foreach (explode('.', $key) as $segment) {
            if (!isset($data[$segment])) return '';
            $data = $data[$segment] ?? '';
        }

        if (!is_null($replacement) && !empty($replacement)) {
            return $this->replacement($replacement, $data);
        }

        return $data;
    }

    public function all(string $file): array
    {
        if (!is_readable($file) && !is_file($file)) {
            throw new \Exception($file . ' not exists');
        }
        return require_once $file;
    }

    private function replacement(array $replacement, string $data)
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

    private function checkReplacement(array $replacement)
    {
        $key = array_keys($replacement)[0];
        $value = array_values($replacement)[0];

        if (!is_string($key) || !preg_match('/^:[a-zA-Z0-9]+/', $key)) {
            throw new \Exception($key . 'key replacement parameter not in valid shape');
        }

        if (!is_string($value) || !preg_match('/[a-zA-Z0-9]+/', $value)) {
            throw new \Exception($value . ' value replacement parameter should be string');
        }
    }

    private function detectCase(string $string)
    {
        $string = substr($string, 1);

        if (preg_match('/\b[A-Z0-9]+[a-z0-9]+\b/', $string)) return 'pascal';

        if (preg_match('/\b[A-Z0-9]+\b/', $string)) return 'upper';

        if (preg_match('/\b[a-z0-9]+\b/', $string)) return 'lower';
    }
}
