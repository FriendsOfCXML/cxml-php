<?php

namespace Mathielen\CXml\Credential;

use Mathielen\CXml\Exception\CXmlCredentialInvalidException;
use Mathielen\CXml\Model\Credential;

interface CredentialRepositoryInterface
{
    /**
     * @throws CXmlCredentialInvalidException
     */
    public function getCredentialByDomainAndId(string $domain, string $identity): Credential;
}
