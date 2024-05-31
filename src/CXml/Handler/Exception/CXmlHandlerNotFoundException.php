<?php

namespace CXml\Handler\Exception;

use CXml\Exception\CXmlNotImplementedException;

class CXmlHandlerNotFoundException extends CXmlNotImplementedException
{
    public function __construct(string $handlerId, \Throwable $previous = null)
    {
        parent::__construct(\sprintf('Handler for %s not found. Register first.', $handlerId), $previous);
    }
}
