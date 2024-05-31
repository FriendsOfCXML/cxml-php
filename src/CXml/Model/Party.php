<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Party
{
    #[Serializer\SerializedName('Credential')]
    private Credential $credential;

    #[Serializer\SerializedName('UserAgent')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $userAgent = null;

    public function __construct(Credential $credential, string $userAgent = null)
    {
        $this->credential = $credential;
        $this->userAgent = $userAgent;
    }

    public function getCredential(): Credential
    {
        return $this->credential;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }
}
