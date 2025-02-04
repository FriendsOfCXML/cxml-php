<?php

declare(strict_types=1);

namespace CXml\Processor;

use CXml\Authentication\AuthenticatorInterface;
use CXml\Context;
use CXml\Credential\CredentialValidatorInterface;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Header;

readonly class HeaderProcessor
{
    public function __construct(
        private CredentialValidatorInterface $credentialValidator,
        private AuthenticatorInterface $authenticator,
    ) {
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
        $this->credentialValidator->validate($header->from->credential);

        $this->credentialValidator->validate($header->to->credential);

        $this->credentialValidator->validate($header->sender->credential);
    }
}
