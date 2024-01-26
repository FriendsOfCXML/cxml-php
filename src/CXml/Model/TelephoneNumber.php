<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class TelephoneNumber
{
    /**
     * @Ser\SerializedName("CountryCode")
     * @Ser\XmlElement (cdata=false)
     */
    private CountryCode $countryCode;

    /**
     * @Ser\SerializedName("AreaOrCityCode")
     * @Ser\XmlElement (cdata=false)
     */
    private ?string $areaOrCityCode = null;

    /**
     * @Ser\SerializedName("Number")
     * @Ser\XmlElement (cdata=false)
     */
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
