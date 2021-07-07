<?php

namespace Mathielen\CXml\Credential;

use Mathielen\CXml\Exception\CXmlAuthenticationInvalidException;
use Mathielen\CXml\Model\Credential;

interface CredentialAuthenticatorInterface
{
    /**
     * @throws CXmlAuthenticationInvalidException
     */
    public function authenticate(Credential $senderCredential): void;
}
