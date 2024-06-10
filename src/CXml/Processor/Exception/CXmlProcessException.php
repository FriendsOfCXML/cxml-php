<?php

declare(strict_types=1);

namespace CXml\Processor\Exception;

use CXml\Exception\CXmlException;
use Throwable;

class CXmlProcessException extends CXmlException
{
    public function __construct(Throwable $previous)
    {
        parent::__construct('Error while processing cXML message: ' . $previous->getMessage(), $previous);
    }
}
