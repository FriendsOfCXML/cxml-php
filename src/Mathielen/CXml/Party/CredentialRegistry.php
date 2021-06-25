<?php

namespace Mathielen\CXml\Party;

use Mathielen\CXml\Exception\CXmlCredentialInvalid;
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

    /**
     * @throws CXmlCredentialInvalid
     */
    public function getCredentialByDomainAndId(string $domain, string $identity): Credential
    {
        foreach ($this->registeredCredentials as $registeredCredential) {
            if ($registeredCredential->getDomain() === $domain && $registeredCredential->getIdentity() === $identity) {
                return $registeredCredential;
            }
        }

        throw new CXmlCredentialInvalid("Could not find credentials for '$identity@$domain'.");
    }
}
