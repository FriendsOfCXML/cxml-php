<?php

namespace Mathielen\CXml\Exception;

use Mathielen\CXml\Model\Credential;

class CXmlAuthenticationInvalid extends CXmlCredentialInvalid
{
    public function __construct(Credential $credential, \Throwable $previous = null)
    {
        parent::__construct("Given shared secret does not match.", $credential, $previous);
    }
}
