<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['name'])]
readonly class Country
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('isoCountryCode')]
        private string $isoCountryCode,
        #[Serializer\XmlValue(cdata: false)]
        private ?string $name = null,
    ) {
    }

    public function getIsoCountryCode(): string
    {
        return $this->isoCountryCode;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
