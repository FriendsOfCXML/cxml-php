<?php

namespace Mathielen\CXml\Model\Message;

use JMS\Serializer\Annotation as Ser;

class ProductActivityHeader
{
	/**
	 * @Ser\SerializedName("messageID")
	 * @Ser\XmlAttribute
	 */
	private string $messageId;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $processType;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?\DateTime $creationDate;

	public function __construct(string $messageId, ?string $processType = null, \DateTime $creationDate = null)
	{
		$this->messageId = $messageId;
		$this->processType = $processType;
		$this->creationDate = $creationDate;
	}
}
