<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class CountryCode
{
    #[Ser\XmlAttribute]
    #[Ser\SerializedName('isoCountryCode')]
    private string $isoCountryCode;

    #[Ser\XmlValue(cdata: false)]
    private ?string $name = null;

    public function __construct(string $isoCountryCode, string $name = null)
    {
        $this->isoCountryCode = $isoCountryCode;
        $this->name = $name;
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
