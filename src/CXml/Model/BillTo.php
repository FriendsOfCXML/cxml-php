<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class BillTo
{
	/**
	 * @Ser\SerializedName("Address")
	 */
	private Address $address;

	public function __construct(Address $address)
	{
		$this->address = $address;
	}
}
