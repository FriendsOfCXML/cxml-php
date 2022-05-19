<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class UnitOfMeasure
{
	/**
	 * @Ser\XmlValue(cdata=false)
	 */
	private string $value;

	public function __construct(string $value)
	{
		$this->value = $value;
	}

	public function getValue(): string
	{
		return $this->value;
	}
}
