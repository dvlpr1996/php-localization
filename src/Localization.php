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

    public function lang(string $key, array $replacement = []): array|string
    {
        $file = $this->getTranslateFile($key);
        $translateKey = $this->getTranslateKey($key);

        if ($this->config->driver === 'json')
            $translateKey = $this->getTranslateKey($this->config->defaultLang . '.' . $key);

        if (is_array($translateKey))
            return $this->localizator->all($file);

        if (is_string($translateKey))
            $text = $this->localizator->get($translateKey, $this->data($file), $replacement);

        return safeText($text);
    }

    private function data(string $file): array
    {
        return [
            'file' => $file,
            'defaultLang' => $this->config->defaultLang,
            'fallBackLang' => $this->config->fallBackLang,
        ];
    }

    private function getLocalizatorClassName(string $className): string
    {
        $fullClassName =  $this->fullClassName($className);

        return class_exists($fullClassName)
            ? $fullClassName
            : throw new \Exception($className . ' Localizator not exists');
    }

    private function fullClassName(string $className): string
    {
        return self::LOCALIZATOR_NAMESPACE . ucwords($className . 'Localizator');
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
        if (empty($key))
            throw new \Exception('key parameter can not be empty');

        $key = explode('.', $key);

        $extension = match ($this->config->driver) {
            'array' => '.php',
            'json' =>  '.json',
        };

        $translateFilePath = match ($extension) {
            '.php' => $this->baseLanguagePath() . '/' . $key[0] . $extension,
            '.json' => $this->baseLanguagePath() . $extension,
        };

        return checkFile($translateFilePath)
            ? $translateFilePath
            : throw new \Exception($translateFilePath . ' not exists');
    }

    private function baseLanguagePath(): string
    {
        $baseLanguagePath = $this->config->langDir . $this->config->defaultLang;

        return checkFile($baseLanguagePath)
            ? $baseLanguagePath
            : throw new \Exception($baseLanguagePath . ' not exists');
    }

    public function __toString(): string
    {
        return __CLASS__;
    }
}
