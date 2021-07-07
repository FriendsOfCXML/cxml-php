<?php

namespace Mathielen\CXml\Processor\Exception;

use Mathielen\CXml\Exception\CXmlException;

class CXmlProcessException extends CXmlException
{
    public function __construct(\Throwable $previous)
    {
        parent::__construct('Error while processing message: '.$previous->getMessage(), $previous);
    }
}
