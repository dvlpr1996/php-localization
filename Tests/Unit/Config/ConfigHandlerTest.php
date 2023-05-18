<?php

declare(strict_types=1);

namespace PhpLocalization\Tests\Config;

use ReflectionObject;
use PHPUnit\Framework\TestCase;
use PhpLocalization\Config\ConfigHandler;
use PhpLocalization\Exceptions\File\FileException;
use PhpLocalization\Exceptions\PropertyNotExistsException;
use PhpLocalization\Exceptions\Config\ConfigInvalidValueException;

/**
 * @covers ConfigHandler
 */
final class ConfigHandlerTest extends TestCase
{
    private ConfigHandler $config;
    private string $langDir = __DIR__ . '/../../../lang/';
    private string $invalidDriver = 'jsonp';

    private function configData(string $d = 'array', string $dl = 'en', string $fl = 'fa'): array
    {
        return [
            'driver' => $d,
            'langDir' => $this->langDir,
            'defaultLang' => $dl,
            'fallBackLang' => $fl
        ];
    }

    private function getMethodNameByReflectionObject(string $name)
    {
        $class = new ReflectionObject($this->config);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function setUp(): void
    {
        $this->config = new ConfigHandler($this->configData());
    }

    public function testConstructorCanAssignProperty(): void
    {
        $this->assertEquals($this->config->driver, 'array');
        $this->assertEquals($this->config->langDir, realpath($this->langDir));
        $this->assertEquals($this->config->defaultLang, realpath($this->langDir . 'en'));
        $this->assertEquals($this->config->fallBackLang, realpath($this->langDir . 'fa'));
    }

    public function testCheckDriverMethodCanReturnDriverValueIfItIsValid(): void
    {
        $method = $this->getMethodNameByReflectionObject('checkDriver');
        $method->invokeArgs($this->config, [$this->config->driver]);

        $this->assertEquals($this->configData()['driver'], $this->config->driver);
    }

    public function testCheckDriverMethodCanThrowConfigInvalidValueException(): void
    {
        $this->expectException(ConfigInvalidValueException::class);

        $method = $this->getMethodNameByReflectionObject('checkDriver');
        $method->invokeArgs($this->config, [$this->invalidDriver]);
    }

    public function testCheckDefaultLangMethodCanValidationLangDirPath(): void
    {
        $method = $this->getMethodNameByReflectionObject('checkDefaultLang');
        $checkDefaultLang = $method->invokeArgs(
            $this->config,
            [
                $this->configData()['defaultLang']
            ]
        );

        $this->assertDirectoryExists($checkDefaultLang);
        $this->assertDirectoryIsReadable($checkDefaultLang);
        $this->assertDirectoryIsWritable($checkDefaultLang);
    }

    public function testCheckDefaultLangMethodCanThrowFileException(): void
    {
        $this->expectException(FileException::class);

        $method = $this->getMethodNameByReflectionObject('checkDefaultLang');
        $method->invokeArgs($this->config, [$this->langDir . 'fr']);
    }

    public function testCheckFallBackLangMethodCanValidationLangDirPath(): void
    {
        $method = $this->getMethodNameByReflectionObject('checkFallBackLang');
        $checkFallBackLang = $method->invokeArgs(
            $this->config,
            [
                $this->configData()['fallBackLang']
            ]
        );

        $this->assertDirectoryExists($checkFallBackLang);
        $this->assertDirectoryIsReadable($checkFallBackLang);
        $this->assertDirectoryIsWritable($checkFallBackLang);
    }

    public function testCheckFallBackLangMethodCanThrowFileException(): void
    {
        $this->expectException(FileException::class);

        $method = $this->getMethodNameByReflectionObject('checkFallBackLang');
        $method->invokeArgs($this->config, [$this->langDir . 'fr']);
    }

    public function testCheckFallBackLangMethodCanReturnNullIfNotDefined(): void
    {
        $method = $this->getMethodNameByReflectionObject('checkFallBackLang');
        $checkFallBackLang = $method->invokeArgs($this->config, ['']);

        $this->assertNull($checkFallBackLang);
    }

    public function testCallingUndefinedPropertyCanThrowPropertyNotExistsException(): void
    {
        $this->expectException(PropertyNotExistsException::class);
        $this->config->undefinedProperty;
    }

    public function testCallingPropertyCanReturnStringIfPropertyExists(): void
    {
        $configs = [
            $this->config->driver,
            $this->config->langDir,
            $this->config->defaultLang,
            $this->config->fallBackLang
        ];

        foreach ($configs as $key) {
            $this->assertIsString($key);
        }
    }

    public function testCheckDirectoryMethodCanReturnPathIfExists(): void
    {
        $method = $this->getMethodNameByReflectionObject('checkDirectory');
        $checkDirectory = $method->invokeArgs($this->config, [
            $this->langDir . $this->configData()['fallBackLang']
        ]);

        $this->assertIsString($checkDirectory);
        $this->assertDirectoryExists($checkDirectory);
        $this->assertDirectoryIsReadable($checkDirectory);
        $this->assertDirectoryIsWritable($checkDirectory);
        $this->assertMatchesRegularExpression('/[a-zA-Z0-9-_\:]/', $checkDirectory);
    }
}
