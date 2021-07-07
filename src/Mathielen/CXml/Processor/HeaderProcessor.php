<?php

namespace Mathielen\CXml\Processor;

use Mathielen\CXml\Credential\CredentialAuthenticatorInterface;
use Mathielen\CXml\Exception\CXmlAuthenticationInvalidException;
use Mathielen\CXml\Exception\CXmlCredentialInvalidException;
use Mathielen\CXml\Model\Credential;
use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Credential\CredentialRepositoryInterface;

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
        $existingCredential = $this->credentialRepository->getCredentialByDomainAndId(
            $testCredential->getDomain(),
            $testCredential->getIdentity()
        );

        if (!$existingCredential) {
            throw new CXmlCredentialInvalidException("Could not find credentials", $testCredential);
        }
    }

    /**
     * @throws CXmlAuthenticationInvalidException
     * @throws CXmlCredentialInvalidException
     */
    private function authenticateSender(Credential $testCredential): void
    {
        $actualCredential = $this->credentialRepository->getCredentialByDomainAndId(
            $testCredential->getDomain(),
            $testCredential->getIdentity()
        );

        if (!$actualCredential) {
            throw new CXmlCredentialInvalidException("Could not find credentials", $testCredential);
        }

        $this->credentialAuthenticator->authenticate($testCredential);
    }
}
