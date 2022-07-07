<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Tax
{
	/**
	 * @Ser\SerializedName("Money")
	 */
	private Money $money;

	/**
	 * @Ser\SerializedName("Description")
	 * @Ser\XmlElement (cdata=false)
	 */
	private MultilanguageString $description;

	public function __construct(string $currency, int $value, MultilanguageString $description)
	{
		$this->money = new Money($currency, $value);
		$this->description = $description;
	}

	public function getMoney(): Money
	{
		return $this->money;
	}

	public function getDescription(): MultilanguageString
	{
		return $this->description;
	}
}
