<?php

namespace Mathielen\CXml\Handler\Exception;

use Mathielen\CXml\Exception\CXmlException;
use Mathielen\CXml\Exception\CXmlNotImplementedException;
use Mathielen\CXml\Model\Credential;

class CXmlHandlerNotFoundException extends CXmlNotImplementedException
{
    public function __construct(string $handlerId, \Throwable $previous = null)
    {
        parent::__construct("Handler for $handlerId not found. Register first.", $previous);
    }
}
