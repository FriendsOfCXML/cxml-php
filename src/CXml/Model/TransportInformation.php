<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class TransportInformation
{
	/**
	 * @Ser\SerializedName("ShippingContractNumber")
	 */
	private ?ShippingContractNumber $shippingContractNumber = null;

	public function __construct(?ShippingContractNumber $shippingContractNumber)
	{
		$this->shippingContractNumber = $shippingContractNumber;
	}

	public static function fromContractAccountNumber(string $carrierAccountNo): self
	{
		return new self(new ShippingContractNumber($carrierAccountNo));
	}
}
