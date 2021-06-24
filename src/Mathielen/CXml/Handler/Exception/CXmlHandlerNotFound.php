<?php

namespace Mathielen\CXml\Handler\Exception;

use Mathielen\CXml\Exception\CXmlException;
use Mathielen\CXml\Model\Credential;

class CXmlHandlerNotFound extends CXmlException
{
    public function __construct(string $handlerId, \Throwable $previous = null)
    {
        parent::__construct("Handler for $handlerId not found. Register first.", $previous);
    }
}
