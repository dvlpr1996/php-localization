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

use PhpLocalization\Exceptions\File\FileException;
use PhpLocalization\Config\ConfigHandler as Config;
use PhpLocalization\Exceptions\Localizator\LocalizatorsException;
use PhpLocalization\Exceptions\Localizator\ClassNotFoundException;
use PhpLocalization\Exceptions\Config\MissingConfigOptionsException;
use PhpLocalization\Localizators\Contract\LocalizatorInterface as Localizator;

final class Localization
{
    private string $file;
    private Config $config;
    private Localizator $localizator;
    private const LOCALIZATOR_NAMESPACE = 'PhpLocalization\\Localizators\\';

    public function __construct(array $configs)
    {
        if(empty($configs)) {
            throw new MissingConfigOptionsException();
        }

        $this->config = new Config($configs);
        $this->localizatorSetter($this->config->driver);
    }

    /**
     * Retrieve Lines Of Text From Language File
     * Or Retrieve All Lines From Language File
     *
     * @param string $key
     * @param array $replacement
     * @return array|string
     */
    public function lang(string $key, array $replacement = []): array|string
    {
        $this->file = $this->getTranslateFile($key);
        $translateKey = $this->getTranslateKey($key);

        if ($this->config->isJsonDriver())
            $translateKey = $this->getTranslateKey($this->config->defaultLang . '.' . $key);

        if (is_array($translateKey))
            return $this->getAllDataFromFile();

        if (is_string($translateKey))
            $text = $this->localizator->get($translateKey, $this->data(), $replacement);

        return safeText($text);
    }

    private function getAllDataFromFile(): array
    {
        $data = $this->data();
        $allData = $this->localizator->all($this->file);

        if (empty($allData) && !is_null($data['fallBackLang'])) {
            $fallBackDir = str_replace($data['defaultLang'], $data['fallBackLang'], $data['file']);
            if (!checkFile($fallBackDir))
                throw new FileException($fallBackDir);
            $allData = $this->localizator->all($fallBackDir);
        }
        return $allData;
    }

    /**
     * Prepared Data For Lang Based On Configs
     * @return array
     */
    private function data(): array
    {
        return [
            'file' => $this->file,
            'defaultLang' => $this->config->defaultLang,
            'fallBackLang' => $this->config->fallBackLang,
        ];
    }

    /**
     * Return Localizator Class Name
     *
     * @param mixed $className
     * @throws \PhpLocalization\Exceptions\Localizator\ClassNotFoundException;
     * @return string
     */
    private function getLocalizatorClassName(string $className): string
    {
        $fullClassName =  $this->fullClassName($className);

        return class_exists($fullClassName)
            ? $fullClassName
            : throw new ClassNotFoundException($className . ' Localizator not exists');
    }

    /**
     * Return Full Localizator Class Name
     * @param mixed $className
     * @return string
     */
    private function fullClassName(string $className): string
    {
        return self::LOCALIZATOR_NAMESPACE . ucwords($className . 'Localizator');
    }

    private function localizatorSetter($driver)
    {
        $className = $this->getLocalizatorClassName($driver);
        $this->setLocalizatorStrategy(new $className);
    }

    private function setLocalizatorStrategy(Localizator $localizator)
    {
        $this->localizator = $localizator;
    }

    private function getTranslateKey(string $key): string|array
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
            throw new LocalizatorsException('Key Parameter Can Not Be Empty');

        $key = explode('.', $key);

        $extension = $this->getExtension();

        $translateFilePath = match ($extension) {
            '.php' => $this->baseLanguagePath() . '/' . $key[0] . $extension,
            '.json' => $this->baseLanguagePath() . $extension,
        };

        return checkFile($translateFilePath)
            ? realpath($translateFilePath)
            : throw new FileException($translateFilePath);
    }

    private function getExtension(): string
    {
        return match ($this->config->driver) {
            'array' => '.php',
            'json' =>  '.json',
        };
    }

    private function baseLanguagePath(): string
    {
        return checkFile($this->config->defaultLang)
            ? $this->config->defaultLang
            : throw new FileException($this->config->defaultLang);
    }

    public function __toString(): string
    {
        return "<ul>
            <li>driver : {$this->config->driver}</li>
            <li>langDir : {$this->config->langDir}</li>
            <li>defaultLang : {$this->config->defaultLang}</li>
            <li>fallBackLang : {$this->config->fallBackLang}</li>
        </ul>";
    }
}
