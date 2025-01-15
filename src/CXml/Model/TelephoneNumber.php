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
        private CountryCode $countryCode,
        #[Serializer\SerializedName('AreaOrCityCode')]
        #[Serializer\XmlElement(cdata: false)]
        private string $areaOrCityCode,
        #[Serializer\SerializedName('Number')]
        #[Serializer\XmlElement(cdata: false)]
        private string $number,
        #[Serializer\SerializedName('Extension')]
        #[Serializer\XmlElement(cdata: false)]
        private ?string $extension = null,
    ) {
    }

    public function getCountryCode(): CountryCode
    {
        return $this->countryCode;
    }

    public function getAreaOrCityCode(): string
    {
        return $this->areaOrCityCode;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }
}
