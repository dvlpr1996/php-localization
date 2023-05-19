<?php

declare(strict_types=1);

namespace PhpLocalization\Tests\Localizators;

use PHPUnit\Framework\TestCase;
use PhpLocalization\Exceptions\File\FileException;
use PhpLocalization\Localizators\ArrayLocalizator;

/**
 * @covers AbstractLocalizator
 */
final class ArrayLocalizatorTest extends TestCase
{
    private ArrayLocalizator $arrayLocalizator;

    private function getMethodNameByReflectionObject(string $name)
    {
        $class = new \ReflectionObject($this->arrayLocalizator);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    private function data(): array
    {
        return [
            'file' => __DIR__ . '/../../../lang/en/login.php',
            'defaultLang' => __DIR__ . '/../../../lang/en',
            'fallBackLang' => __DIR__ . '/../../../lang/fa'
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
        $this->arrayLocalizator = new ArrayLocalizator;
    }

    public function testGetMethodCanReturnLinesOfTextFromLanguageFile(): void
    {
        $method = $this->getMethodNameByReflectionObject('get');
        $get = $method->invokeArgs($this->arrayLocalizator, [
            'hello',
            $this->data(),
            $this->replacement()
        ]);

        $this->assertIsString($get);
        $this->assertEquals('hello welcome to our web site dear JOHN and DOE', $get);
    }

    public function testGetMethodCanReturnEmptyStringIfKeyDoesNotExists(): void
    {
        $method = $this->getMethodNameByReflectionObject('get');
        $get = $method->invokeArgs($this->arrayLocalizator, [
            'by',
            $this->data(),
            $this->replacement()
        ]);

        $this->assertIsString($get);
        $this->assertEmpty($get);
        $this->assertEquals('', $get);
    }

    public function testGetMethodCanReturnFallback(): void
    {
        $method = $this->getMethodNameByReflectionObject('get');
        $get = $method->invokeArgs($this->arrayLocalizator, [
            'hi',
            $this->data(),
            $this->replacement()
        ]);

        $this->assertIsString($get);
        $this->assertEquals('سلام JOHN به وب سایت ما خوش امدید', $get);
    }

    public function testAllMethodCanReturnAllDateFromTranslateFile(): void
    {
        $method = $this->getMethodNameByReflectionObject('all');
        $all = $method->invokeArgs($this->arrayLocalizator, [$this->data()['file']]);

        $this->assertNotEmpty($all);
        $this->assertIsArray($all);
    }

    public function testAllMethodCanThrowFileException(): void
    {
        $this->expectException(FileException::class);

        $method = $this->getMethodNameByReflectionObject('all');
        $method->invokeArgs($this->arrayLocalizator, [$this->data()['file'] . '../']);
    }
}
