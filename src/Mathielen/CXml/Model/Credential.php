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
     */
    private string $identity;

    /**
     * @Ser\SerializedName("CredentialMac")
     */
    //private CredentialMac $credentialMac; TODO

    /**
     * @Ser\SerializedName("SharedSecret")
     */
    private ?string $sharedSecret;

    public function __construct(string $domain, string $identity, ?string $sharedSecret = null)
    {
        $this->domain = $domain;
        $this->identity = $identity;
        $this->sharedSecret = $sharedSecret;
    }
}
