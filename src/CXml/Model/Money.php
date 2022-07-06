<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Money
{
	/**
	 * @Ser\XmlAttribute
	 */
	private string $currency;

	/**
	 * @Ser\XmlValue(cdata=false)
	 */
	private string $value;

	public function __construct(string $currency, int $value)
	{
		$this->currency = $currency;
		$this->value = \number_format($value / 100, 2, '.', '');
	}

	public function getCurrency(): string
	{
		return $this->currency;
	}

	public function getValue(): string
	{
		return $this->value;
	}
}
