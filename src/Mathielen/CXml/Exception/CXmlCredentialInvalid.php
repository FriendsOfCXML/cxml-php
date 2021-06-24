<?php

namespace Mathielen\CXml\Exception;

use Mathielen\CXml\Model\Credential;

class CXmlCredentialInvalid extends CXmlException
{
    private Credential $credential;

    public function __construct(string $message, Credential $credential, \Throwable $previous = null)
    {
        parent::__construct($message."\nCredential:\n".$credential, $previous);

        $this->credential = $credential;
    }

    public function getCredential(): Credential
    {
        return $this->credential;
    }
}
