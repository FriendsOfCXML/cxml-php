<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['carrierIdentifiers', 'shipmentIdentifiers'])]
class ShipControl
{
    /**
     * @var CarrierIdentifier[]
     */
    #[Serializer\XmlList(entry: 'CarrierIdentifier', inline: true)]
    #[Serializer\Type('array<CXml\Model\CarrierIdentifier>')]
    private array $carrierIdentifiers = [];

    /**
     * @var ShipmentIdentifier[]
     */
    #[Serializer\XmlList(entry: 'ShipmentIdentifier', inline: true)]
    #[Serializer\Type('array<CXml\Model\ShipmentIdentifier>')]
    private array $shipmentIdentifiers = [];

    public function __construct(
        CarrierIdentifier $carrierIdentifier,
        ShipmentIdentifier $shipmentIdentifier,
    ) {
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
            if ($carrierIdentifier->domain === $domain) {
                return $carrierIdentifier->value;
            }
        }

        return null;
    }

    public function getShipmentIdentifier(?string $domain = null): ?string
    {
        foreach ($this->shipmentIdentifiers as $shipmentIdentifier) {
            if ($shipmentIdentifier->domain === $domain) {
                return $shipmentIdentifier->value;
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
