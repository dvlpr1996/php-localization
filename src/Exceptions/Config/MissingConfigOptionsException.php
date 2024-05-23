<?php

namespace dvlpr1996\PhpLocalization\Exceptions\Config;

class MissingConfigOptionsException extends \Exception
{
    public function __construct(int $statusCode = 500, $previous = null)
    {
        parent::__construct('Config Array Is Not Valid', $statusCode, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . " [{$this->code}]: {$this->message}\n";
    }
}
