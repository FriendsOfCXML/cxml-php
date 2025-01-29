<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['countryCode', 'areaOrCityCode', 'number'])]
readonly class TelephoneNumber
{
    public function __construct(
        #[Serializer\SerializedName('CountryCode')]
        #[Serializer\XmlElement(cdata: false)]
        public CountryCode $countryCode,
        #[Serializer\SerializedName('AreaOrCityCode')]
        #[Serializer\XmlElement(cdata: false)]
        public string $areaOrCityCode,
        #[Serializer\SerializedName('Number')]
        #[Serializer\XmlElement(cdata: false)]
        public string $number,
        #[Serializer\SerializedName('Extension')]
        #[Serializer\XmlElement(cdata: false)]
        public ?string $extension = null,
    ) {
    }
}
