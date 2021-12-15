<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ShipmentIdentifier
{
	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $domain = null;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $trackingNumberDate = null;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $trackingURL = null;

	/**
	 * @Ser\XmlValue(cdata=false)
	 */
	private string $value;

	public function __construct(string $value, ?string $domain = null, ?string $trackingNumberDate = null, ?string $trackingURL = null)
	{
		$this->value = $value;
		$this->domain = $domain;
		$this->trackingNumberDate = $trackingNumberDate;
		$this->trackingURL = $trackingURL;
	}

	public function getDomain(): ?string
	{
		return $this->domain;
	}

	public function getValue(): string
	{
		return $this->value;
	}

	public function getTrackingNumberDate(): ?string
	{
		return $this->trackingNumberDate;
	}

	public function getTrackingURL(): ?string
	{
		return $this->trackingURL;
	}
}
