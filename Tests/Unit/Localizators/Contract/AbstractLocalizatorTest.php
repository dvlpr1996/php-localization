<?php

declare(strict_types=1);

namespace dvlpr1996\PhpLocalization\Tests\Localizators\Contract;

use PHPUnit\Framework\TestCase;
use dvlpr1996\PhpLocalization\Localizators\Contract\AbstractLocalizator;
use dvlpr1996\PhpLocalization\Exceptions\Localizator\LocalizatorsException;

/**
 * @covers AbstractLocalizator
 */
final class AbstractLocalizatorTest extends TestCase
{
    private AbstractLocalizator $abstractLocalizator;

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

    public function testCheckReplacementKeyMethodCanValidateReplacementParam(): void
    {
        $method = $this->getMethodNameByReflectionObject('checkReplacementKey');
        $checkReplacementKey = $method->invokeArgs($this->abstractLocalizator, [
            [
                ':FNAME' => 'john',
                ':LNAME' => 'doe'
            ]
        ]);

        $this->assertNull($checkReplacementKey);
    }

    public function testCheckReplacementKeyMethodCanThrowLocalizatorsExceptionForKey(): void
    {
        $this->expectException(LocalizatorsException::class);

        $method = $this->getMethodNameByReflectionObject('checkReplacementKey');
        $method->invokeArgs($this->abstractLocalizator, [
            [
                '' => 'john',
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
