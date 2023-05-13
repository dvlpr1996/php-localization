<?php

/**
 * @Package: Php Localization Package
 * @Class  : Localization
 * @Author : Nima jahan bakhshian / dvlpr1996 <nimajahanbakhshian@gmail.com>
 * @URL    : https://github.com/dvlpr1996
 * @License: MIT License Copyright (c) 2023 (until present) Nima jahan bakhshian
 */

declare(strict_types=1);

namespace PhpLocalization;

use PhpLocalization\Config\ConfigHandler as Config;
use PhpLocalization\Localizators\Contract\LocalizatorInterface as Localizator;

class Localization
{
    private Config $config;
    private Localizator $localizator;
    private const LOCALIZATOR_NAMESPACE = 'PhpLocalization\\Localizators\\';

    public function __construct(array $configs = [])
    {
        $this->config = new Config($configs);
        $this->localizatorSetter($this->config->driver);
    }

    private function getLocalizatorClassName(string $className): string
    {
        $fullClassName =  self::LOCALIZATOR_NAMESPACE . ucwords($className . 'Localizator');
        return class_exists($fullClassName)
            ? $fullClassName
            : throw new \Exception($className . ' Localizator not exists');
    }

    private function localizatorSetter($driver)
    {
        $className = $this->getLocalizatorClassName($driver);
        $this->setLocalizatorClass(new $className);
    }

    private function setLocalizatorClass(Localizator $localizator)
    {
        $this->localizator = $localizator;
    }

    public function lang(string $key, array $replacement = [])
    {
        $file = $this->getTranslateFile($key);
        $translateKey = $this->getTranslateKey($key);

        if (is_array($translateKey)) {
            return $this->localizator->all($file);
        }

        // todo : e func for return
        if (is_string($translateKey)) {
            return $this->localizator->get($file, $translateKey, $replacement);
        }
    }

    private function getTranslateKey(string $key)
    {
        $keys = explode('.', $key);
        if (count($keys) > 1) {
            unset($keys[0]);
            return implode('.', $keys);
        }
        return $keys;
    }

    private function getTranslateFile(string $key)
    {
        if (empty($key)) {
            throw new \Exception('key parameter can not be empty');
        }

        $key = explode('.', $key);

        $extension = match ($this->config->driver) {
            'array' => '.php',
            'json' =>  '.json',
        };

        $dir = $this->config->langDir . $this->config->defaultLang . '/' . $key[0] . $extension;

        if (!is_readable($dir) && !is_file($dir)) {
            throw new \Exception($dir . ' not exists');
        }

        return $dir;
    }
}
