<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class ShipTo
{
    use IdReferencesTrait;

    #[Serializer\SerializedName('Address')]
    private Address $address;

    /**
     *
     * @var CarrierIdentifier[]
     */
    #[Serializer\XmlList(inline: true, entry: 'CarrierIdentifier')]
    #[Serializer\Type('array<CXml\Model\CarrierIdentifier>')]
    private array $carrierIdentifiers = [];

    #[Serializer\SerializedName('TransportInformation')]
    private ?TransportInformation $transportInformation = null;

    public function __construct(Address $address, TransportInformation $transportInformation = null)
    {
        $this->address = $address;
        $this->transportInformation = $transportInformation;
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
