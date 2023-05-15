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

class ConfigHandler
{
    private string $driver;
    private string $langDir;
    private ?string $fallBackLang;
    private string $defaultLang = 'en';
    private array $allowedDrivers = ['array', 'json', 'gettext'];

    public function __construct(array $config = [])
    {
        $this->driver = $this->resolveConfigKey($config['driver']);
        $this->langDir = $this->resolveConfigKey($config['langDir']);
        $this->defaultLang = $this->resolveConfigKey($config['defaultLang']);
        $this->fallBackLang = $this->resolveConfigKey($config['fallBackLang']);
    }

    public function __get(string $property)
    {
        if (!property_exists($this, $property)) {
            throw new \Exception($property . ' not exists');
        }

        return match ($property) {
            'driver' => $this->checkDriver($this->$property),
            'langDir' =>  $this->checkLangDir($this->$property),
            'defaultLang' =>  $this->checkDefaultLang($this->$property),
            'fallBackLang' =>  $this->checkFallBckLang($this->$property),
        };
    }
    public function __toString(): string
    {
        return __CLASS__;
    }

    private function checkDriver(string $driver)
    {
        return in_array(strtolower($driver), $this->allowedDrivers)
            ? $driver
            : throw new \Exception($driver . ' not allowed');
    }

    private function checkLangDir(string $path)
    {
        return (is_dir($path))
            ? $path
            : throw new \Exception($path . ' not exists');
    }

    private function checkDefaultLang(string $defaultLang)
    {
        return (is_dir($this->langDir . $defaultLang))
            ? $defaultLang
            : throw new \Exception($defaultLang . ' not exists');
    }

    private function checkFallBckLang(?string $fallBckLang)
    {
        if (is_null($fallBckLang) || empty($fallBckLang))
            return;

        return (is_dir($this->langDir . $fallBckLang))
            ? $fallBckLang
            : throw new \Exception($fallBckLang . ' not exists');
    }

    private function resolveConfigKey(?string $configKey)
    {
        return (!empty($configKey) || !is_null($configKey))
            ? $configKey
            : throw new \Exception('Config array Key can not be empty or null');
    }
}
