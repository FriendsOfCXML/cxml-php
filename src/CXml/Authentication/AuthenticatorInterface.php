<?php

namespace CXml\Authentication;

use CXml\Context;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Model\Header;

interface AuthenticatorInterface
{
	/**
	 * @throws CXmlAuthenticationInvalidException
	 */
	public function authenticate(Header $header, Context $context): void;
}
