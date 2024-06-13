<?php

namespace CXml\Processor;

use CXml\Authentication\AuthenticatorInterface;
use CXml\Context;
use CXml\Credential\CredentialRepositoryInterface;
use CXml\Credential\CredentialValidatorInterface;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Header;

class HeaderProcessor
{
    private CredentialRepositoryInterface $credentialRepository;
    private CredentialValidatorInterface $credentialValidator;
    private AuthenticatorInterface $authenticator;

    public function __construct(
        CredentialRepositoryInterface $credentialRepository,
        CredentialValidatorInterface $credentialValidator,
        AuthenticatorInterface $authenticator
    ) {
        $this->credentialRepository = $credentialRepository;
        $this->credentialValidator = $credentialValidator;
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
        $this->credentialValidator->validate($header->getFrom()->getCredential());

        $this->credentialValidator->validate($header->getTo()->getCredential());

        $this->credentialValidator->validate($header->getSender()->getCredential());
    }
}
