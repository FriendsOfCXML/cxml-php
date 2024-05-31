<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class CarrierIdentifier
{
    public const DOMAIN_SCAC = 'SCAC';
    public const DOMAIN_COMPANYNAME = 'companyName';
    public const DOMAIN_SKU = 'sku';
    public const DOMAIN_CARRIER_METHOD = 'carrierMethod';

    #[Serializer\XmlAttribute]
    private string $domain;

    #[Serializer\XmlValue(cdata: false)]
    private string $value;

    public function __construct(string $domain, string $value)
    {
        $this->domain = $domain;
        $this->value = $value;
    }

    public static function fromScacCode(string $scacCarrierCode): self
    {
        return new self(self::DOMAIN_SCAC, $scacCarrierCode);
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
