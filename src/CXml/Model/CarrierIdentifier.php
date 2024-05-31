<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value'])]
class CarrierIdentifier
{
    public const DOMAIN_SCAC = 'SCAC';

    public const DOMAIN_COMPANYNAME = 'companyName';

    public const DOMAIN_SKU = 'sku';

    public const DOMAIN_CARRIER_METHOD = 'carrierMethod';

    public function __construct(
        #[Serializer\XmlAttribute]
        private readonly string $domain,
        #[Serializer\XmlValue(cdata: false)]
        private readonly string $value,
    ) {
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
