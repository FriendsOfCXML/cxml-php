<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class PaymentService
{
    use IdReferencesTrait;
    use ExtrinsicsTrait;

    public function __construct(
        #[Serializer\XmlAttribute]
        readonly private string $method,
        #[Serializer\XmlAttribute]
        readonly private ?string $provider = null,
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }
}
