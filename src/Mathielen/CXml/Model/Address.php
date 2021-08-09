<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Address
{
	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("addressID")
	 */
	private ?string $addressId;

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("addressIDDomain")
	 */
	private ?string $addressIdDomain;

	/**
	 * @Ser\SerializedName("Name")
	 * @Ser\XmlElement (cdata=false)
	 */
	private MultilanguageString $name;

	/**
	 * @Ser\SerializedName("PostalAddress")
	 */
	private ?PostalAddress $postalAddress;

	/**
	 * @Ser\SerializedName("Email")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $email;

	/**
	 * @Ser\SerializedName("Phone")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $phone;

	/**
	 * @Ser\SerializedName("Fax")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $fax;

	/**
	 * @Ser\SerializedName("URL")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $url;

	public function __construct(MultilanguageString $name, ?PostalAddress $postalAddress = null, ?string $addressId = null, ?string $addressIdDomain = null, ?string $email = null, ?string $phone = null, ?string $fax = null, ?string $url = null)
	{
		$this->addressId = $addressId;
		$this->addressIdDomain = $addressIdDomain;
		$this->name = $name;
		$this->postalAddress = $postalAddress;
		$this->email = $email;
		$this->phone = $phone;
		$this->fax = $fax;
		$this->url = $url;
	}
}
