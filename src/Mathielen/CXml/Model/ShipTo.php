<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ShipTo
{
	/**
	 * @Ser\SerializedName("Address")
	 */
	private Address $address;

	/**
	 * @Ser\XmlList(inline=true, entry="CarrierIdentifier")
	 * @Ser\Type("array<Mathielen\CXml\Model\CarrierIdentifier>")
	 *
	 * @var CarrierIdentifier[]
	 */
	private array $carrierIdentifiers = [];

	/**
	 * @Ser\SerializedName("TransportInformation")
	 */
	private ?TransportInformation $transportInformation;

	public function __construct(Address $address, ?TransportInformation $transportInformation = null)
	{
		$this->address = $address;
		$this->transportInformation = $transportInformation;
	}

	public function addCarrierIdentifier(string $domain, string $identifier): self
	{
		$this->carrierIdentifiers[] = new CarrierIdentifier($domain, $identifier);

		return $this;
	}
}
