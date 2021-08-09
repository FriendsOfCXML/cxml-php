<?php

namespace Mathielen\CXml\Credential;

use Mathielen\CXml\Exception\CXmlAuthenticationInvalidException;
use Mathielen\CXml\Exception\CXmlCredentialInvalidException;
use Mathielen\CXml\Model\Credential;

class CredentialRegistry implements CredentialRepositoryInterface, CredentialAuthenticatorInterface
{
	/**
	 * @var Credential[]
	 */
	private array $registeredCredentials = [];

	public function registerCredential(Credential $credential): void
	{
		$this->registeredCredentials[] = $credential;
	}

	/**
	 * @throws CXmlCredentialInvalidException
	 */
	public function getCredentialByDomainAndId(string $domain, string $identity): Credential
	{
		foreach ($this->registeredCredentials as $registeredCredential) {
			if ($registeredCredential->getDomain() === $domain && $registeredCredential->getIdentity() === $identity) {
				return $registeredCredential;
			}
		}

		throw new CXmlCredentialInvalidException("Could not find credentials for '$identity@$domain'.");
	}

	/**
	 * @throws CXmlAuthenticationInvalidException
	 * @throws CXmlCredentialInvalidException
	 */
	public function authenticate(Credential $senderCredential): void
	{
		$baseCredential = $this->getCredentialByDomainAndId(
			$senderCredential->getDomain(),
			$senderCredential->getIdentity(),
		);

		if ($baseCredential->getSharedSecret() !== $senderCredential->getSharedSecret()) {
			throw new CXmlAuthenticationInvalidException($senderCredential);
		}
	}
}