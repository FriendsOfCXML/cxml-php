<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value'])]
readonly class ShipmentIdentifier
{
    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        public string $value,
        #[Serializer\XmlAttribute]
        public ?string $domain = null,
        #[Serializer\XmlAttribute]
        public ?string $trackingNumberDate = null,
        #[Serializer\XmlAttribute]
        public ?string $trackingURL = null,
    ) {
    }
}
