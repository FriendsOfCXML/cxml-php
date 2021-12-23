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
	 * @Ser\SerializedName("CarrierIdentifier")
	 */
	private ?CarrierIdentifier $carrierIdentifier;

	/**
	 * @Ser\SerializedName("TransportInformation")
	 */
	private ?TransportInformation $transportInformation;

	public function __construct(Address $address, ?CarrierIdentifier $carrierIdentifier = null, ?TransportInformation $transportInformation = null)
	{
		$this->address = $address;
		$this->carrierIdentifier = $carrierIdentifier;
		$this->transportInformation = $transportInformation;
	}
}
