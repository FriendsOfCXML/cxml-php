<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class ShipControl
{
    /**
     * @var CarrierIdentifier[]
     */
    #[Serializer\XmlList(inline: true, entry: 'CarrierIdentifier')]
    #[Serializer\Type('array<CXml\Model\CarrierIdentifier>')]
    private array $carrierIdentifiers = [];

    /**
     * @var ShipmentIdentifier[]
     */
    #[Serializer\XmlList(inline: true, entry: 'ShipmentIdentifier')]
    #[Serializer\Type('array<CXml\Model\ShipmentIdentifier>')]
    private array $shipmentIdentifiers = [];

    public function __construct(CarrierIdentifier $carrierIdentifier, ShipmentIdentifier $shipmentIdentifier)
    {
        $this->carrierIdentifiers[] = $carrierIdentifier;
        $this->shipmentIdentifiers[] = $shipmentIdentifier;
    }

    public static function create(CarrierIdentifier $carrierIdentifier, ShipmentIdentifier $shipmentIdentifier): self
    {
        return new self($carrierIdentifier, $shipmentIdentifier);
    }

    public function addCarrierIdentifier(string $domain, string $value): self
    {
        $this->carrierIdentifiers[] = new CarrierIdentifier($domain, $value);

        return $this;
    }

    public function getCarrierIdentifier(string $domain): ?string
    {
        foreach ($this->carrierIdentifiers as $carrierIdentifier) {
            if ($carrierIdentifier->getDomain() === $domain) {
                return $carrierIdentifier->getValue();
            }
        }

        return null;
    }

    public function getShipmentIdentifier(string $domain = null): ?string
    {
        foreach ($this->shipmentIdentifiers as $shipmentIdentifier) {
            if ($shipmentIdentifier->getDomain() === $domain) {
                return $shipmentIdentifier->getValue();
            }
        }

        return null;
    }

    public function getCarrierIdentifiers(): array
    {
        return $this->carrierIdentifiers;
    }

    public function getShipmentIdentifiers(): array
    {
        return $this->shipmentIdentifiers;
    }
}
