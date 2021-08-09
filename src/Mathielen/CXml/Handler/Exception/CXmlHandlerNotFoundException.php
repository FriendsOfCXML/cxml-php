<?php

namespace Mathielen\CXml\Handler\Exception;

use Mathielen\CXml\Exception\CXmlNotImplementedException;

class CXmlHandlerNotFoundException extends CXmlNotImplementedException
{
	public function __construct(string $handlerId, \Throwable $previous = null)
	{
		parent::__construct("Handler for $handlerId not found. Register first.", $previous);
	}
}
