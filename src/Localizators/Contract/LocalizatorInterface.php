<?php

/**
 * @Package: Php Localization Package
 * @Interface  : LocalizatorInterface
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace PhpLocalization\Localizators\Contract;

interface LocalizatorInterface
{
    public function get(string $key, array $data, array $replacement = []): string;

    public function all(string $file): array;
}
