<?php

namespace CXml\Credential;

use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Model\Credential;

interface CredentialAuthenticatorInterface
{
	/**
	 * @throws CXmlAuthenticationInvalidException
	 */
	public function authenticate(Credential $senderCredential): void;
}
