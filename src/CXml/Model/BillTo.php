<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class BillTo
{
	use IdReferencesTrait;

	/**
	 * @Ser\SerializedName("Address")
	 */
	private Address $address;

	public function __construct(Address $address)
	{
		$this->address = $address;
	}

	public function getAddress(): Address
	{
		return $this->address;
	}
}
