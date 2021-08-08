<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Comment
{
    /**
     * @Ser\SerializedName("Attachment")
     */
    private ?Url $attachment;

    /**
     * @Ser\XmlValue(cdata=false)
     */
    private ?string $value;

	/**
	 * @Ser\XmlAttribute(namespace="http://www.w3.org/XML/1998/namespace")
	 */
	private ?string $lang;

    public function __construct(?string $value = null, ?string $lang = null, ?string $attachment = null)
    {
        $this->value = $value;
        $this->lang = $lang;
		$this->attachment = $attachment ? new Url($attachment) : $attachment;
    }

	public function getAttachment()
	{
		return $this->attachment;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	public function getLang(): ?string
	{
		return $this->lang;
	}

}
