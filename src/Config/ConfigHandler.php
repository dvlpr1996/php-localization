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
use PhpLocalization\Exceptions\Config\ConfigInvalidValueException;
use PhpLocalization\Exceptions\Config\MissingConfigOptionsException;

final class ConfigHandler
{
    private string $driver;
    private string $langDir;
    private ?string $fallBackLang;
    private string $defaultLang = 'en';
    private array $allowedDrivers = ['array', 'json', 'gettext'];
    private array $allowedConfigs = [
        'driver', 'langDir', 'defaultLang', 'fallBackLang'
    ];

    public function __construct(array $configs)
    {
        $this->checkConfigs($configs);

        $this->driver = $configs['driver'];
        $this->langDir = $configs['langDir'];
        $this->defaultLang = $configs['defaultLang'];
        $this->fallBackLang = $configs['fallBackLang'];
    }

    /**
     * Validation Configs
     *
     * @param array $configs
     * @throws \PhpLocalization\Exceptions\Config\MissingConfigOptionsException
     * @throws \PhpLocalization\Exceptions\Config\ConfigInvalidValueException
     * @return void
     */
    private function checkConfigs(array $configs): void
    {
        $diffConfigs = array_diff($this->allowedConfigs, array_values(array_keys($configs)));

        if (!empty($diffConfigs))
            throw new MissingConfigOptionsException();

        foreach ($configs as $key => $value) {
            if (!is_string($value) || empty($value))
                throw new ConfigInvalidValueException('Value Can Not Be Empty Or Null');
        }
    }

    public function __get(string $property)
    {
        if (!property_exists($this, $property))
            throw new \Exception($property . 'Not Exists');

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

    /**
     * Validation Driver
     *
     * @param string $driver
     * @throws \PhpLocalization\Exceptions\Config\ConfigInvalidValueException
     * @return string
     * Driver If Is Valid Otherwise Return ConfigInvalidValueException
     */
    private function checkDriver(string $driver)
    {
        return in_array(strtolower($driver), $this->allowedDrivers)
            ? $driver
            : throw new ConfigInvalidValueException($driver . ' Driver Not Allowed');
    }

    /**
     * Validation LangDir Path
     *
     * @param string $path
     * @throws \PhpLocalization\Exceptions\File\FileException
     * @return string
     * langDir if Exists or return FileException
     */
    private function checkLangDir(string $path)
    {
        return (is_dir($path)) ? $path : throw new FileException($path);
    }

    /**
     * Validation defaultLang Path
     *
     * @param string $path
     * @throws \PhpLocalization\Exceptions\File\FileException
     * @return string
     * defaultLang if Exists or return FileException
     */
    private function checkDefaultLang(string $defaultLang)
    {
        return (is_dir($this->langDir . $defaultLang))
            ? $defaultLang
            : throw new FileException($defaultLang);
    }

    /**
     * validation FallBckLang
     *
     * @param string|null $fallBckLang
     * @throws \PhpLocalization\Exceptions\File\FileException
     * @return $fallBckLang if isset or exists
     */
    private function checkFallBckLang(?string $fallBckLang)
    {
        if (is_null($fallBckLang) || empty($fallBckLang))
            return;

        return (is_dir($this->langDir . $fallBckLang))
            ? $fallBckLang
            : throw new FileException($fallBckLang);
    }
}
