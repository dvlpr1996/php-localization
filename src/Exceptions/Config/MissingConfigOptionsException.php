<?php

namespace PhpLocalization\Exceptions\Config;

class MissingConfigOptionsException extends \Exception
{
    public function __construct(int $statusCode = 500, $previous = null)
    {
        parent::__construct('Config Array Is Not Valid', $statusCode, $previous);
    }
}
