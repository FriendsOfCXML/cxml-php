<?php

namespace CXml\Credential;

use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Credential;

interface CredentialRepositoryInterface
{
	/**
	 * @throws CXmlCredentialInvalidException
	 */
	public function getCredentialByDomainAndId(string $domain, string $identity): Credential;
}
