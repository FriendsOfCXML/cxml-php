<?php

namespace CXml\Credential;

use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Credential;

interface CredentialValidatorInterface
{
    /**
     * @throws CXmlCredentialInvalidException
     */
    public function validate(Credential $credential): void;
}
