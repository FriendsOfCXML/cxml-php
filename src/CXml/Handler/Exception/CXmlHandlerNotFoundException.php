<?php

namespace CXml\Handler\Exception;

use CXml\Exception\CXmlNotImplementedException;

class CXmlHandlerNotFoundException extends CXmlNotImplementedException
{
	public function __construct(string $handlerId, \Throwable $previous = null)
	{
		parent::__construct("Handler for {$handlerId} not found. Register first.", $previous);
	}
}
