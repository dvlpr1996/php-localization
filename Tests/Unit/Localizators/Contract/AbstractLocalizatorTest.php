<?php

declare(strict_types=1);

namespace PhpLocalization\Tests\Localizators\Contract;

use PHPUnit\Framework\TestCase;
use PhpLocalization\Localizators\Contract\AbstractLocalizator;
use PhpLocalization\Exceptions\Localizator\LocalizatorsException;

/**
 * @covers AbstractLocalizator
 */
final class AbstractLocalizatorTest extends TestCase
{
    private AbstractLocalizator $abstractLocalizator;
    private string $file = __DIR__ . '/../../../../lang/en/login.php';
    private string $fallBack = __DIR__ . '/../../../../lang/fa/login.php';

    private function getMethodNameByReflectionObject(string $name)
    {
        $class = new \ReflectionObject($this->abstractLocalizator);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function setUp(): void
    {
        $this->abstractLocalizator = $this->getMockForAbstractClass(AbstractLocalizator::class);
    }

    public function testCheckReplacementMethodCanValidateReplacementParam(): void
    {
        $method = $this->getMethodNameByReflectionObject('checkReplacement');
        $checkReplacement = $method->invokeArgs($this->abstractLocalizator, [
            [
                ':FNAME' => 'john',
                ':LNAME' => 'doe'
            ]
        ]);

        $this->assertNull($checkReplacement);
    }

    public function testCheckReplacementMethodCanThrowLocalizatorsExceptionForKey(): void
    {
        $this->expectException(LocalizatorsException::class);

        $method = $this->getMethodNameByReflectionObject('checkReplacement');
        $method->invokeArgs($this->abstractLocalizator, [
            [
                '' => 'john',
            ]
        ]);
    }
    public function testCheckReplacementMethodCanThrowLocalizatorsExceptionForValue(): void
    {
        $this->expectException(LocalizatorsException::class);

        $method = $this->getMethodNameByReflectionObject('checkReplacement');
        $method->invokeArgs($this->abstractLocalizator, [
            [
                ':fname' => '',
            ]
        ]);
    }

    public function testDetectCaseMethodCanDetectReplacementKeyCase(): void
    {
        $method = $this->getMethodNameByReflectionObject('detectCase');
        $case = $method->invokeArgs($this->abstractLocalizator, [':Fname']);

        $this->assertIsString($case);
        $this->assertEquals('pascal', $case);
    }

    public function testReplacementMethodCanReplace(): void
    {
        $method = $this->getMethodNameByReflectionObject('replacement');
        $case = $method->invokeArgs($this->abstractLocalizator, [
            [
                ':Fname' => 'john'
            ],
            'hi :fname how are you'
        ]);

        $this->assertIsString($case);
        $this->assertEquals('hi John how are you', $case);
    }
}
