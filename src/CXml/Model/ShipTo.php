<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['address', 'carrierIdentifiers', 'transportInformation', 'idReferences'])]
class ShipTo
{
    use IdReferencesTrait;

    /**
     * @var CarrierIdentifier[]
     */
    #[Serializer\XmlList(entry: 'CarrierIdentifier', inline: true)]
    #[Serializer\Type('array<CXml\Model\CarrierIdentifier>')]
    private array $carrierIdentifiers = [];

    public function __construct(#[Serializer\SerializedName('Address')]
        public readonly Address $address, #[Serializer\SerializedName('TransportInformation')]
        public readonly ?TransportInformation $transportInformation = null)
    {
    }

    public function addCarrierIdentifier(string $domain, string $identifier): self
    {
        $this->carrierIdentifiers[] = new CarrierIdentifier($domain, $identifier);

        return $this;
    }
}
