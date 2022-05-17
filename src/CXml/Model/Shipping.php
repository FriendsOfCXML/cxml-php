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
	private ?MultilanguageString $description;

	public function __construct(Money $money, ?MultilanguageString $description = null)
	{
		$this->money = $money;
		$this->description = $description;
	}
}
