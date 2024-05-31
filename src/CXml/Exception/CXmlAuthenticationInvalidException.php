<?php

declare(strict_types=1);

namespace CXml\Exception;

use CXml\Model\Credential;

class CXmlAuthenticationInvalidException extends CXmlCredentialInvalidException
{
    public function __construct(Credential $credential)
    {
        parent::__construct('Given shared secret does not match.', $credential);
    }
}
