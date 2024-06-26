<?php

/**
 * @Package: Php Localization Package
 * @Class  : ArrayLocalizator
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace dvlpr1996\PhpLocalization\Localizators;

use dvlpr1996\PhpLocalization\Exceptions\File\FileException;
use dvlpr1996\PhpLocalization\Localizators\Contract\AbstractLocalizator as Localizator;

final class ArrayLocalizator extends Localizator
{
    public function get(string $key, array $data, array $replacement = []): string
    {
        return $this->getDataByArray($data['file'], $key, $replacement, $this->fallBack($data));
    }

    public function all(string $file): array
    {
        return checkFile($file)
            ? require $file
            : throw new FileException($file);
    }

    public function __toString(): string
    {
        return __CLASS__;
    }
}
