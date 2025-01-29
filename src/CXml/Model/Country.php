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
        public string $isoCountryCode,
        #[Serializer\XmlValue(cdata: false)]
        public ?string $name = null,
    ) {
    }
}
