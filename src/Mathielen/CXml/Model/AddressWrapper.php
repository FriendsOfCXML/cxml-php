<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class AddressWrapper
{
	/**
	 * @Ser\SerializedName("Address")
	 */
	private Address $address;

	public function __construct(MultilanguageString $name, ?PostalAddress $postalAddress = null, ?string $addressId = null, ?string $addressIdDomain = null, ?string $email = null, ?string $phone = null, ?string $fax = null, ?string $url = null)
	{
		$this->address = new Address(
			$name,
			$postalAddress,
			$addressId,
			$addressIdDomain,
			$email,
			$phone,
			$fax,
			$url,
		);
	}
}
