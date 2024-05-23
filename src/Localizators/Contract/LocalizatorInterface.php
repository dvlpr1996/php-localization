<?php

/**
 * @Package: Php Localization Package
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace dvlpr1996\PhpLocalization\Localizators\Contract;

interface LocalizatorInterface
{
    /**
     * retrieve lines of text from language file
     *
     * @param string $key
     * @param array $data
     * @param array $replacement
     * @return string
     */
    public function get(string $key, array $data, array $replacement = []): string;

    /**
     * retrieve all lines from language file
     *
     * @param string $file
     * @return array
     */
    public function all(string $file): array;
}
