<?php

declare(strict_types=1);

namespace CXml\Authentication;

use CXml\Context;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Model\Header;

readonly class SimpleSharedSecretAuthenticator implements AuthenticatorInterface
{
    public function __construct(private string $sharedSecret)
    {
    }

    public function authenticate(Header $header, Context $context): void
    {
        // use hash_equals() for constant-time comparison to prevent timing attacks
        if (!hash_equals($this->sharedSecret, (string)$header->sender->credential->getSharedSecret())) {
            throw new CXmlAuthenticationInvalidException($header->sender->credential);
        }
    }
}
