<?php

declare(strict_types=1);

namespace CXml\Credential;

use CXml\Authentication\AuthenticatorInterface;
use CXml\Context;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Credential;
use CXml\Model\Header;

use function sprintf;
class Registry implements CredentialRepositoryInterface, AuthenticatorInterface, CredentialValidatorInterface
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
            if ($registeredCredential->getDomain() !== $domain) {
                continue;
            }

            if ($registeredCredential->getIdentity() !== $identity) {
                continue;
            }

            return $registeredCredential;
        }

        throw new CXmlCredentialInvalidException(sprintf("Could not find credentials for '%s@%s'.", $identity, $domain));
    }

    /**
     * @throws CXmlAuthenticationInvalidException
     * @throws CXmlCredentialInvalidException
     */
    public function authenticate(Header $header, Context $context): void
    {
        $senderCredential = $header->getSender()->getCredential();

        $baseCredential = $this->getCredentialByDomainAndId(
            $senderCredential->getDomain(),
            $senderCredential->getIdentity(),
        );

        if ($baseCredential->getSharedSecret() !== $senderCredential->getSharedSecret()) {
            throw new CXmlAuthenticationInvalidException($senderCredential);
        }
    }

    /**
     * @throws CXmlCredentialInvalidException
     */
    public function validate(Credential $credential): void
    {
        // provoke an exception if credential was not found
        $this->getCredentialByDomainAndId(
            $credential->getDomain(),
            $credential->getIdentity()
        );
    }
}
