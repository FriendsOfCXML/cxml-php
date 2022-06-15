<?php

namespace CXml\Processor;

use CXml\Credential\CredentialAuthenticatorInterface;
use CXml\Credential\CredentialRepositoryInterface;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Credential;
use CXml\Model\Header;

class HeaderProcessor
{
	private CredentialRepositoryInterface $credentialRepository;
	private CredentialAuthenticatorInterface $credentialAuthenticator;

	public function __construct(CredentialRepositoryInterface $credentialRepository, CredentialAuthenticatorInterface $credentialAuthenticator)
	{
		$this->credentialRepository = $credentialRepository;
		$this->credentialAuthenticator = $credentialAuthenticator;
	}

	/**
	 * @throws CXmlCredentialInvalidException
	 * @throws CXmlAuthenticationInvalidException
	 */
	public function process(Header $header): void
	{
		$this->validatePartys($header);

		$this->authenticateSender($header->getSender()->getCredential());
	}

	/**
	 * @throws CXmlCredentialInvalidException
	 */
	private function validatePartys(Header $header): void
	{
		$this->checkCredentialIsValid($header->getFrom()->getCredential());

		$this->checkCredentialIsValid($header->getTo()->getCredential());

		$this->checkCredentialIsValid($header->getSender()->getCredential());
	}

	/**
	 * @throws CXmlCredentialInvalidException
	 */
	private function checkCredentialIsValid(Credential $testCredential): void
	{
		// provoke an exception if credential was not found
		$this->credentialRepository->getCredentialByDomainAndId(
			$testCredential->getDomain(),
			$testCredential->getIdentity()
		);
	}

	/**
	 * @throws CXmlAuthenticationInvalidException
	 * @throws CXmlCredentialInvalidException
	 */
	private function authenticateSender(Credential $testCredential): void
	{
		$this->checkCredentialIsValid($testCredential);

		$this->credentialAuthenticator->authenticate($testCredential);
	}
}
