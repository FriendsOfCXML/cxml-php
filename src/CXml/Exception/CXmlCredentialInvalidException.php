<?php

declare(strict_types=1);

namespace CXml\Exception;

use CXml\Model\Credential;

class CXmlCredentialInvalidException extends CXmlExpectationFailedException
{
    public function __construct(string $message, private readonly ?Credential $credential = null, \Throwable $previous = null)
    {
        parent::__construct($message . ($credential instanceof Credential ? "\nCredential was:" . $credential : ''), $previous);
    }

    public function getCredential(): ?Credential
    {
        return $this->credential;
    }
}
