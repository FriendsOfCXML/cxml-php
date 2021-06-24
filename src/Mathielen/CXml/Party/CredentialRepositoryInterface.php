<?php

namespace Mathielen\CXml\Party;

use Mathielen\CXml\Model\Credential;

interface CredentialRepositoryInterface
{
    public function findCredentialByDomainAndId(string $domain, string $id): ?Credential;
}
