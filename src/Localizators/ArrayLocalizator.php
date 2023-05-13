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

use PhpLocalization\Localizators\Contract\AbstractLocalizator as Localizator;

class ArrayLocalizator extends Localizator
{
    public function get(string $file, string $key, array $replacement = []): string
    {
        return $this->getDataByArray($file, $key, $replacement);
    }

    public function all(string $file): array
    {
        if (!is_readable($file) && !is_file($file)) {
            throw new \Exception($file . ' not exists');
        }
        return require_once $file;
    }
}
