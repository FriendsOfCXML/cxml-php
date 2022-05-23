<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Description extends MultilanguageString
{
	/**
	 * @Ser\SerializedName("ShortName")
	 * @Ser\XmlElement(cdata=true)
	 */
	private ?string $shortName = null;

	public function __construct(?string $value, ?string $type = null, string $lang = 'en')
	{
		parent::__construct($value, $type, $lang);
	}

	public static function createWithShortName(string $shortName, ?string $type = null, string $lang = 'en'): self
	{
		$new = new self(null, $type, $lang);
		$new->setShortname($shortName);

		return $new;
	}

	public function setShortname(?string $shortName): void
	{
		$this->shortName = $shortName;
	}

}
