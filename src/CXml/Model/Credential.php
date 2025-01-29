<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;
use Stringable;

use function sprintf;

#[Serializer\AccessorOrder(order: 'custom', custom: ['identity', 'sharedSecret'])]
class Credential implements Stringable
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public readonly string $domain,
        #[Serializer\SerializedName('Identity')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly string $identity,
        #[Serializer\SerializedName('SharedSecret')]
        #[Serializer\XmlElement(cdata: false)]
        private ?string $sharedSecret = null,
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s@%s', $this->identity, $this->domain);
    }

    public function setSharedSecret(string $sharedSecret): self
    {
        $this->sharedSecret = $sharedSecret;

        return $this;
    }

    public function getSharedSecret(): ?string
    {
        return $this->sharedSecret;
    }
}
