<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Credential
{
    #[Serializer\XmlAttribute]
    private string $domain;

    #[Serializer\SerializedName('Identity')]
    #[Serializer\XmlElement(cdata: false)]
    private string $identity;

    // private CredentialMac $credentialMac; TODO
    #[Serializer\SerializedName('SharedSecret')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $sharedSecret = null;

    public function __construct(string $domain, string $identity, string $sharedSecret = null)
    {
        $this->domain = $domain;
        $this->identity = $identity;
        $this->sharedSecret = $sharedSecret;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }

    public function getSharedSecret(): ?string
    {
        return $this->sharedSecret;
    }

    public function setSharedSecret(?string $sharedSecret): void
    {
        $this->sharedSecret = $sharedSecret;
    }

    public function __toString(): string
    {
        return \sprintf('%s@%s', $this->identity, $this->domain);
    }
}
