<?php

namespace CXml\Authentication;

use CXml\Context;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Model\Header;

class SimpleSharedSecretAuthenticator implements AuthenticatorInterface
{
    public function __construct(private readonly string $sharedSecret)
    {
    }

    public function authenticate(Header $header, Context $context): void
    {
        if ($this->sharedSecret !== $header->getSender()->getCredential()->getSharedSecret()) {
            throw new CXmlAuthenticationInvalidException($header->getSender()->getCredential());
        }
    }
}
