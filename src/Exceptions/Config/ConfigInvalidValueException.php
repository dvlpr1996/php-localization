<?php

namespace PhpLocalization\Exceptions\Config;

class ConfigInvalidValueException extends \Exception
{
    public function __construct(string $message, int $statusCode = 500, $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ' : ' . $this->message;
    }
}
