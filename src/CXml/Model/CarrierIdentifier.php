<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value'])]
class CarrierIdentifier
{
    final public const DOMAIN_COMPANYNAME = 'companyName';

    final public const DOMAIN_SCAC = 'SCAC';

    final public const DOMAIN_IATA = 'IATA';

    final public const DOMAIN_AAR = 'AAR';

    final public const DOMAIN_UIC = 'UIC';

    final public const DOMAIN_EAN = 'EAN';

    final public const DOMAIN_DUNS = 'DUNS';

    public function __construct(
        #[Serializer\XmlAttribute]
        private readonly string $domain,
        #[Serializer\XmlValue(cdata: false)]
        private readonly string $value,
    ) {
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
