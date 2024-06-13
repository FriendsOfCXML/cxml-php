<?php

declare(strict_types=1);

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
