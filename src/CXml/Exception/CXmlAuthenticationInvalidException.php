<?php

namespace CXml\Exception;

use CXml\Model\Credential;

class CXmlAuthenticationInvalidException extends CXmlCredentialInvalidException
{
	public function __construct(Credential $credential, \Throwable $previous = null)
	{
		parent::__construct('Given shared secret does not match.', $credential, $previous);
	}
}
