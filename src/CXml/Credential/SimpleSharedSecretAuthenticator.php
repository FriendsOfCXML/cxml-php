<?php

namespace CXml\Credential;

use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Model\Credential;

class SimpleSharedSecretAuthenticator implements CredentialAuthenticatorInterface
{
	private string $sharedSecret;

	public function __construct(string $sharedSecret)
	{
		$this->sharedSecret = $sharedSecret;
	}

	public function authenticate(Credential $senderCredential): void
	{
		if ($this->sharedSecret !== $senderCredential->getSharedSecret()) {
			throw new CXmlAuthenticationInvalidException($senderCredential);
		}
	}
}
