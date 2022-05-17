<?php

namespace CXml\Processor\Exception;

use CXml\Exception\CXmlException;

class CXmlProcessException extends CXmlException
{
	public function __construct(\Throwable $previous)
	{
		parent::__construct('Error while processing message: '.$previous->getMessage(), $previous);
	}
}
