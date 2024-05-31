<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value'])]
readonly class ShipmentIdentifier
{
    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        private string $value,
        #[Serializer\XmlAttribute]
        private ?string $domain = null,
        #[Serializer\XmlAttribute]
        private ?string $trackingNumberDate = null,
        #[Serializer\XmlAttribute]
        private ?string $trackingURL = null
    ) {
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getTrackingNumberDate(): ?string
    {
        return $this->trackingNumberDate;
    }

    public function getTrackingURL(): ?string
    {
        return $this->trackingURL;
    }
}
