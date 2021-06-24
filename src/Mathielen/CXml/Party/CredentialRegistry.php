<?php

namespace Mathielen\CXml\Party;

use Mathielen\CXml\Model\Credential;

class CredentialRegistry implements CredentialRepositoryInterface
{
    /**
     * @var Credential[]
     */
    private array $registeredCredentials = [];

    public function registerCredential(Credential $credential): void
    {
        $this->registeredCredentials[] = $credential;
    }

    public function findCredentialByDomainAndId(string $domain, string $id): ?Credential
    {
        foreach ($this->registeredCredentials as $registeredCredential) {
            if ($registeredCredential->getDomain() === $domain && $registeredCredential->getIdentity() === $id) {
                return $registeredCredential;
            }
        }

        return null;
    }
}
