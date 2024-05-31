<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class TelephoneNumber
{
    #[Serializer\SerializedName('CountryCode')]
    #[Serializer\XmlElement(cdata: false)]
    private CountryCode $countryCode;

    #[Serializer\SerializedName('AreaOrCityCode')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $areaOrCityCode = null;

    #[Serializer\SerializedName('Number')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $number = null;

    public function __construct(CountryCode $countryCode, string $areaOrCityCode = null, string $number = null)
    {
        $this->countryCode = $countryCode;
        $this->areaOrCityCode = $areaOrCityCode;
        $this->number = $number;
    }

    public function getCountryCode(): CountryCode
    {
        return $this->countryCode;
    }

    public function getAreaOrCityCode(): ?string
    {
        return $this->areaOrCityCode;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }
}
