<?php

declare(strict_types=1);

namespace CXml\Exception;

use Exception;
use Throwable;

class CXmlException extends Exception
{
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
