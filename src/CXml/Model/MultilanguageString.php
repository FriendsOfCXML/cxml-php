<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class MultilanguageString
{
	/**
	 * @Ser\XmlValue(cdata=false)
	 */
	private ?string $value;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $type;

	/**
	 * @Ser\XmlAttribute(namespace="http://www.w3.org/XML/1998/namespace")
	 */
	private ?string $lang;

	public function __construct(?string $value, ?string $type = null, string $lang = 'en')
	{
		$this->value = $value;
		$this->lang = $lang;
		$this->type = $type;
	}
}
