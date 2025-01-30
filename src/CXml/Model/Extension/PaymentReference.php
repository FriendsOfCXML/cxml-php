<?php

declare(strict_types=1);

namespace CXml\Model\Extension;

use CXml\Model\ExtensionInterface;
use CXml\Model\Money;
use CXml\Model\Trait\ExtrinsicsTrait;
use CXml\Model\Trait\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

class PaymentReference implements ExtensionInterface
{
    use IdReferencesTrait;
    use ExtrinsicsTrait;

    public function __construct(
        #[Serializer\SerializedName('Money')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly Money $money,
        #[Serializer\XmlAttribute]
        public readonly string $method,
        #[Serializer\XmlAttribute]
        public readonly ?string $provider = null,
    ) {
    }

    public static function create(Money $money, string $method, ?string $provider = null): self
    {
        return new self($money, $method, $provider);
    }
}
