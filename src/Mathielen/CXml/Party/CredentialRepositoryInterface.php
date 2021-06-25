<?php

namespace Mathielen\CXml\Party;

use Mathielen\CXml\Exception\CXmlCredentialInvalid;
use Mathielen\CXml\Model\Credential;

interface CredentialRepositoryInterface
{
    /**
     * @throws CXmlCredentialInvalid
     */
    public function getCredentialByDomainAndId(string $domain, string $identity): Credential;
}
