<?php

namespace Mathielen\CXml\Credential;

use Mathielen\CXml\Exception\CXmlAuthenticationInvalid;
use Mathielen\CXml\Model\Credential;

interface CredentialAuthenticatorInterface
{
    /**
     * @throws CXmlAuthenticationInvalid
     */
    public function authenticate(Credential $senderCredential): void;
}
