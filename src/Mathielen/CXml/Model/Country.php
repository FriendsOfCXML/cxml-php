<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Country
{
    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("isoCountryCode")
     */
    private string $isoCountryCode;

    /**
     * @Ser\XmlValue(cdata=false)
     */
    private string $name;

    public function getIsoCountryCode(): string
    {
        return $this->isoCountryCode;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
