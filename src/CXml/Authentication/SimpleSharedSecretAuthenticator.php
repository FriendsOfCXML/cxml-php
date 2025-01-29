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
        if ($this->sharedSecret !== $header->sender->credential->sharedSecret) {
            throw new CXmlAuthenticationInvalidException($header->sender->credential);
        }
    }
}
