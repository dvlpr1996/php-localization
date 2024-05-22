<?php

/**
 * @Package: Php Localization Package
 * @Class  : ConfigHandler
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace PhpLocalization\Config;

use PhpLocalization\Exceptions\File\FileException;
use PhpLocalization\Exceptions\PropertyNotExistsException;
use PhpLocalization\Exceptions\Config\ConfigInvalidValueException;
use PhpLocalization\Exceptions\Config\MissingConfigOptionsException;

final class ConfigHandler
{
    private string $driver;
    private string $langDir;
    private ?string $fallBackLang;
    private string $defaultLang = 'en';

    private array $allowedDrivers = ['array', 'json'];

    private array $allowedConfigs = [
        'driver', 'langDir', 'defaultLang', 'fallBackLang'
    ];

    public function __construct(array $configs)
    {
        $this->checkConfigs($configs);

        $this->driver = $configs['driver'];
        $this->langDir = $configs['langDir'];
        $this->defaultLang = $configs['defaultLang'];
        $this->fallBackLang = $configs['fallBackLang'] ?? null;
    }

    /**
     * Validation Configs
     *
     * @param array $configs
     * @throws \PhpLocalization\Exceptions\Config\MissingConfigOptionsException
     * @throws \PhpLocalization\Exceptions\Config\ConfigInvalidValueException
     * @return void
     */
    public function checkConfigs(array $configs): void
    {
        $diffConfigs = array_diff($this->allowedConfigs, array_keys($configs));

        if (!empty($diffConfigs)) {
            throw new MissingConfigOptionsException();
        }

        foreach ($configs as $key => $value) {
            if (($key === 'fallBackLang') && (is_null($value) || empty($value))) {
                continue;
            }

            if (!is_string($value) || empty($value)) {
                throw new ConfigInvalidValueException('Value Can Not Be Empty Or Null');
            }
        }
    }

    public function __get(string $property)
    {
        if (!property_exists($this, $property)) {
            throw new PropertyNotExistsException($property);
        }

        return match ($property) {
            'driver' => $this->checkDriver($this->$property),
            'langDir' =>  $this->checkDirectory($this->$property),
            'defaultLang' =>  $this->checkDefaultLang($this->$property),
            'fallBackLang' =>  $this->checkFallBackLang($this->$property),
            default => throw new PropertyNotExistsException($property),
        };
    }

    public function __toString(): string
    {
        return "<ul>
            <li>driver: {$this->driver}</li>
            <li>langDir: {$this->langDir}</li>
            <li>defaultLang: {$this->defaultLang}</li>
            <li>fallBackLang: {$this->fallBackLang}</li>
        </ul>";
    }

    public function isJsonDriver(): bool
    {
        return $this->driver === 'json';
    }

    /**
     * Validation Driver
     *
     * @param string $driver
     * @throws \PhpLocalization\Exceptions\Config\ConfigInvalidValueException
     * @return string
     * Driver If Is Valid Otherwise Return ConfigInvalidValueException
     */
    private function checkDriver(string $driver): string
    {
        return in_array(strtolower($driver), $this->allowedDrivers)
            ? $driver
            : throw new ConfigInvalidValueException($driver . ' Driver Not Allowed');
    }

    /**
     * Validation defaultLang Path
     *
     * @param string $path
     * @throws \PhpLocalization\Exceptions\File\FileException
     * @return string
     * defaultLang if Exists or return FileException
     */
    private function checkDefaultLang(string $defaultLang): string
    {
        return $this->checkDirectory($this->langDir . $defaultLang);
    }

    /**
     * validation FallBckLang
     *
     * @param string|null $fallBckLang
     * @throws \PhpLocalization\Exceptions\File\FileException
     * @return string|null
     */
    private function checkFallBackLang(?string $fallBckLang): ?string
    {
        if (is_null($fallBckLang) || empty($fallBckLang)) {
            return null;
        }

        return $this->checkDirectory($this->langDir . $fallBckLang);
    }

    private function checkDirectory(string $path): string|false
    {
        return (is_dir($path)) ? realpath($path) : throw new FileException($path);
    }
}
