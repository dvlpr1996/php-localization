<?php

declare(strict_types=1);

namespace PhpLocalization\Tests\Localizators;

use PHPUnit\Framework\TestCase;
use PhpLocalization\Localizators\JsonLocalizator;
use PhpLocalization\Exceptions\File\FileException;
use PhpLocalization\Exceptions\Localizator\JsonValidationException;

/**
 * @covers JsonLocalizator
 */
final class JsonLocalizatorTest extends TestCase
{
    private JsonLocalizator $jsonLocalizator;

    private function getMethodNameByReflectionObject(string $name)
    {
        $class = new \ReflectionObject($this->jsonLocalizator);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    private function data(): array
    {
        return [
            'file' => __DIR__ . '/../../../lang/en.json',
            'defaultLang' => __DIR__ . '/../../../lang/en.json',
            'fallBackLang' => __DIR__ . '/../../../lang/fa.json'
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
        $this->jsonLocalizator = new JsonLocalizator;
    }

    public function testGetMethodCanReturnLinesOfTextFromLanguageFile(): void
    {
        $method = $this->getMethodNameByReflectionObject('get');
        $get = $method->invokeArgs($this->jsonLocalizator, [
            'hi',
            $this->data(),
            $this->replacement()
        ]);

        $this->assertIsString($get);
        $this->assertEquals('hello welcome to our web site dear JOHN and DOE', $get);
    }

    public function testGetMethodCanReturnEmptyStringIfKeyDoesNotExists(): void
    {
        $method = $this->getMethodNameByReflectionObject('get');
        $get = $method->invokeArgs($this->jsonLocalizator, [
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
        $get = $method->invokeArgs($this->jsonLocalizator, [
            'hello',
            $this->data(),
            $this->replacement()
        ]);

        $this->assertIsString($get);
        $this->assertEquals('سلام JOHN به وب سایت ما خوش امدید', $get);
    }

    public function testAllMethodCanReturnAllDateFromTranslateFile(): void
    {
        $method = $this->getMethodNameByReflectionObject('all');
        $all = $method->invokeArgs($this->jsonLocalizator, [$this->data()['file']]);

        $this->assertNotEmpty($all);
        $this->assertIsArray($all);
    }

    public function testAllMethodCanThrowFileException(): void
    {
        $this->expectException(FileException::class);

        $method = $this->getMethodNameByReflectionObject('all');
        $method->invokeArgs($this->jsonLocalizator, [$this->data()['file'] . '../']);
    }

    public function testAllMethodCanThrowJsonValidationException(): void
    {
        $this->expectException(JsonValidationException::class);

        $method = $this->getMethodNameByReflectionObject('all');
        $method->invokeArgs($this->jsonLocalizator, [__DIR__ . '/../../../lang/notJson.json']);
    }

    public function testIsJsonMethodCanReturnFalseIfDataParamIsEmptyOrNull(): void
    {
        $method = $this->getMethodNameByReflectionObject('isJson');
        $isJson = $method->invokeArgs($this->jsonLocalizator, ['']);

        $this->assertIsBool($isJson);
        $this->assertFalse($isJson);
    }

    public function testIsJsonMethodCanReturnFalseIfJsonFileValidationFail(): void
    {
        $method = $this->getMethodNameByReflectionObject('isJson');
        $isJson = $method->invokeArgs(
            $this->jsonLocalizator,
            [__DIR__ . '/../../../lang/notJson.json']
        );

        $this->assertIsBool($isJson);
        $this->assertFalse($isJson);
    }
}
