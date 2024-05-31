<?php

namespace CXml\Model;

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
        private readonly Address $address, #[Serializer\SerializedName('TransportInformation')]
        private readonly ?TransportInformation $transportInformation = null)
    {
    }

    public function addCarrierIdentifier(string $domain, string $identifier): self
    {
        $this->carrierIdentifiers[] = new CarrierIdentifier($domain, $identifier);

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
