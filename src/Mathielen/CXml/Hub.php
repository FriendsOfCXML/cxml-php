<?php

namespace Mathielen\CXml;

use Assert\Assertion;
use Mathielen\CXml\Model\Credential;
use Mathielen\CXml\Model\CXml;

class Hub
{
    private Credential $hubCredentials;

    public function __construct(Credential $hubCredentials)
    {
        Assertion::notNull($hubCredentials->getSharedSecret(), "Shared Secret must be set in hub-credentials");

        $this->hubCredentials = $hubCredentials;
    }

    public function processMessage(CXml $message): void
    {
    }
}
