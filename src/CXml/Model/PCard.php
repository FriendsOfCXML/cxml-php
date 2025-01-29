<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class PCard implements PaymentInterface
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public string $number,
        #[Serializer\XmlAttribute]
        public string $expiration,
        #[Serializer\XmlAttribute]
        public ?string $name = null,
        #[Serializer\SerializedName('PostalAddress')]
        public ?PostalAddress $postalAddress = null,
    ) {
    }
}
