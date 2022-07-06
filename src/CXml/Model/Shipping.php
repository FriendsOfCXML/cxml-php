<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Shipping
{
	/**
	 * @Ser\SerializedName("Money")
	 */
	private Money $money;

	/**
	 * @Ser\SerializedName("Description")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?MultilanguageString $description = null;

	public function __construct(string $currency, int $value, ?MultilanguageString $description = null)
	{
		$this->money = new Money($currency, $value);
		$this->description = $description;
	}

	public function getMoney(): Money
	{
		return $this->money;
	}

	public function getDescription(): ?MultilanguageString
	{
		return $this->description;
	}
}
