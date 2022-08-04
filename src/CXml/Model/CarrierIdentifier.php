<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class CarrierIdentifier
{
	public const DOMAIN_SCAC = 'SCAC';
	public const DOMAIN_COMPANYNAME = 'companyName';
	public const DOMAIN_SKU = 'sku';
	public const DOMAIN_CARRIER_METHOD = 'carrierMethod';

	/**
	 * @Ser\XmlAttribute
	 */
	private string $domain;

	/**
	 * @Ser\XmlValue(cdata=false)
	 */
	private string $value;

	public function __construct(string $domain, string $value)
	{
		$this->domain = $domain;
		$this->value = $value;
	}

	public static function fromScacCode(string $scacCarrierCode): self
	{
		return new static(self::DOMAIN_SCAC, $scacCarrierCode);
	}

	public function getDomain(): string
	{
		return $this->domain;
	}

	public function getValue(): string
	{
		return $this->value;
	}
}
