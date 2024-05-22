<?php

namespace PhpLocalization\Exceptions\Localizator;

class JsonValidationException extends \Exception
{
    public function __construct(string $message, int $statusCode = 500, $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . " [{$this->code}]: {$this->message}\n";
    }
}
