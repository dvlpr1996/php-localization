<?php

declare(strict_types=1);

namespace PhpLocalization\Tests;

use PHPUnit\Framework\TestCase;
use PhpLocalization\Localization;
use PhpLocalization\Exceptions\File\FileException;
use PhpLocalization\Exceptions\Localizator\LocalizatorsException;
use PhpLocalization\Exceptions\Localizator\ClassNotFoundException;

/**
 * @covers Localization
 */
final class LocalizationTest extends TestCase
{
    private Localization $localization;

    private function getMethodNameByReflectionObject(string $name)
    {
        $class = new \ReflectionObject($this->localization);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    private function config(): array
    {
        return [
            'driver' => 'array',
            'langDir' => __DIR__ . '/../../lang/',
            'defaultLang' => 'en',
            'fallBackLang' => 'fa'
        ];
    }

    private function replacement(): array
    {
        return [
            ':FNAME' => 'john',
            ':LNAME' => 'doe'
        ];
    }

    protected function setUp(): void
    {
        $this->localization = new Localization($this->config());
    }

    public function testLangMethodCanReturnArray(): void
    {
        $lang = $this->localization->lang('login', [
            $this->replacement()
        ]);

        $this->assertIsArray($lang);
    }

    public function testLangMethodCanReturnString(): void
    {
        $lang = $this->localization->lang('login.hi', [
            ':FNAME' => 'john',
            ':LNAME' => 'doe'
        ]);

        $this->assertIsString($lang);
    }

    public function testGetLocalizatorClassNameMethodCanReturnString(): void
    {
        $method = $this->getMethodNameByReflectionObject('getLocalizatorClassName');
        $className = $method->invokeArgs($this->localization, [
            $this->config()['driver']
        ]);

        $this->assertIsString($className);
        $this->assertEquals('PhpLocalization\Localizators\ArrayLocalizator', $className);
    }

    public function testGetLocalizatorClassNameMethodCanThrowClassNotFoundException(): void
    {
        $this->expectException(ClassNotFoundException::class);

        $method = $this->getMethodNameByReflectionObject('getLocalizatorClassName');
        $method->invokeArgs($this->localization, ['jsonp']);
    }

    public function testFullClassNameMethodCanReturnFullLocalizatorClassName(): void
    {
        $method = $this->getMethodNameByReflectionObject('fullClassName');
        $className = $method->invokeArgs($this->localization, [
            $this->config()['driver']
        ]);

        $this->assertIsString($className);
        $this->assertEquals('PhpLocalization\Localizators\ArrayLocalizator', $className);
    }

    public function testGetTranslateKeyMethodCanReturnStringWhenKeyContainsDotNotation(): void
    {
        $method = $this->getMethodNameByReflectionObject('getTranslateKey');
        $translateKey = $method->invokeArgs($this->localization, [
            'login.hello'
        ]);

        $this->assertIsString($translateKey);
    }

    public function testGetTranslateKeyMethodCanReturnArrayWhenKeyDoesNotContainDotNotation(): void
    {
        $method = $this->getMethodNameByReflectionObject('getTranslateKey');
        $translateKey = $method->invokeArgs($this->localization, [
            'login'
        ]);

        $this->assertIsArray($translateKey);
    }

    public function testGetTranslateFileMethodCanThrowLocalizatorsException(): void
    {
        $this->expectException(LocalizatorsException::class);

        $method = $this->getMethodNameByReflectionObject('getTranslateFile');
        $method->invokeArgs($this->localization, [
            ''
        ]);
    }

    public function testGetTranslateFileMethodCanThrowFileException(): void
    {
        $this->expectException(FileException::class);

        $method = $this->getMethodNameByReflectionObject('getTranslateFile');
        $method->invokeArgs($this->localization, [
            'validation'
        ]);
    }

    public function testGetTranslateFileMethodCanReturnString(): void
    {
        $method = $this->getMethodNameByReflectionObject('getTranslateFile');
        $translateFile = $method->invokeArgs($this->localization, [
            'login.hello'
        ]);

        $this->assertIsString($translateFile);
        $this->assertFileExists($translateFile);
        $this->assertFileIsReadable($translateFile);
        $this->assertFileIsWritable($translateFile);
    }
}
