<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Credential
{
    /**
     * @Ser\XmlAttribute
     */
    private string $domain;

    /**
     * @Ser\SerializedName("Identity")
     * @Ser\XmlElement (cdata=false)
     */
    private string $identity;

    /**
     * @Ser\SerializedName("CredentialMac")
     * @Ser\XmlElement (cdata=false)
     */
    //private CredentialMac $credentialMac; TODO

    /**
     * @Ser\SerializedName("SharedSecret")
     * @Ser\XmlElement (cdata=false)
     */
    private ?string $sharedSecret;

    public function __construct(string $domain, string $identity, ?string $sharedSecret = null)
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

    public function setSharedSecret(?string $sharedSecret): ?string
    {
        $this->sharedSecret = $sharedSecret;
    }

    public function __toString(): string
    {
        return sprintf('%s@%s', $this->identity, $this->domain);
    }
}
