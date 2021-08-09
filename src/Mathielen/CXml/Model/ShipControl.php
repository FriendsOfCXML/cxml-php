<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ShipControl
{
	/**
	 * @Ser\XmlList(inline=true, entry="CarrierIdentifier")
	 * @Ser\Type("array<Mathielen\CXml\Model\CarrierIdentifier>")
	 *
	 * @var CarrierIdentifier[]
	 */
	private array $carrierIdentifiers = [];

	/**
	 * @Ser\SerializedName("ShipmentIdentifier")
	 */
	private string $shipmentIdentifier;

	public function __construct(string $shipmentIdentifier)
	{
		$this->shipmentIdentifier = $shipmentIdentifier;
	}

	public static function create(string $shipmentIdentifier): self
	{
		return new self($shipmentIdentifier);
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

	public function getShipmentIdentifier(): string
	{
		return $this->shipmentIdentifier;
	}
}
