<?php

namespace CXml\Processor;

use CXml\Authentication\AuthenticatorInterface;
use CXml\Context;
use CXml\Credential\CredentialRepositoryInterface;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Credential;
use CXml\Model\Header;

class HeaderProcessor
{
    private CredentialRepositoryInterface $credentialRepository;
    private AuthenticatorInterface $authenticator;

    public function __construct(CredentialRepositoryInterface $credentialRepository, AuthenticatorInterface $authenticator)
    {
        $this->credentialRepository = $credentialRepository;
        $this->authenticator = $authenticator;
    }

    /**
     * @throws CXmlCredentialInvalidException
     * @throws CXmlAuthenticationInvalidException
     */
    public function process(Header $header, Context $context): void
    {
        $this->validatePartys($header);

        $this->authenticator->authenticate($header, $context);
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
}
