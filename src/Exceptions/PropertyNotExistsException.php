<?php

namespace PhpLocalization\Exceptions;

use Throwable;

class PropertyNotExistsException extends \Exception
{
    protected string $property;

    public function __construct(string $property, int $statusCode = 500, Throwable $previous = null)
    {
        $this->property = $property;
        parent::__construct("The property '{$property}' does not exist.", $statusCode, $previous);
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function __toString(): string
    {
        return __CLASS__ . " [{$this->code}]: {$this->message}\n";
    }
}
