<?php

/**
 * @Package: Php Localization Package
 * @Class  : JsonLocalizator
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace PhpLocalization\Localizators;

use PhpLocalization\Exceptions\File\FileException;
use PhpLocalization\Exceptions\Localizator\JsonValidationException;
use PhpLocalization\Localizators\Contract\AbstractLocalizator as Localizator;

class JsonLocalizator extends Localizator
{
    public function get(string $key, array $data, array $replacement = []): string
    {
        return $this->getDataByArray($data['file'], $key, $replacement, $this->fallBack($data));
    }

    public function all(string $file): array
    {
        if (!checkFile($file))
            throw new FileException($file);

        return ($this->isJson(file_get_contents($file)))
            ? json_decode(file_get_contents($file), true)
            : throw new JsonValidationException('Json File Is Not Valid');
    }

    private function isJson(mixed $data): bool
    {
        if (empty($data) || !is_string($data))
            return false;

        if (!is_array(json_decode($data, true)) && json_last_error() !== JSON_ERROR_NONE)
            return false;

        return true;
    }

}
