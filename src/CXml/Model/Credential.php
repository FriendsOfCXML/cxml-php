<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['identity', 'sharedSecret'])]
class Credential implements \Stringable
{
    public function __construct(
        #[Serializer\XmlAttribute]
        private readonly string $domain,
        #[Serializer\SerializedName('Identity')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly string $identity,
        #[Serializer\SerializedName('SharedSecret')]
        #[Serializer\XmlElement(cdata: false)]
        private ?string $sharedSecret = null
    ) {
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
