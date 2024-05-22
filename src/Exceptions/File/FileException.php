<?php

namespace PhpLocalization\Exceptions\File;

class FileException extends \Exception
{
    public function __construct(string $message, int $statusCode = 500, $previous = null)
    {
        parent::__construct($message . ' File Path Not Exists', $statusCode, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . " [{$this->code}]: {$this->message}\n";
    }
}
