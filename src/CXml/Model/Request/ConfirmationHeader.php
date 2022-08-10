<?php

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\IdReferencesTrait;
use CXml\Model\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Ser;

class ConfirmationHeader
{
	use IdReferencesTrait, ExtrinsicsTrait;
	public const TYPE_ACCEPT = 'accept';
	public const TYPE_ALLDETAIL = 'allDetail';
	public const TYPE_DETAIL = 'detail';
	public const TYPE_BACKORDERED = 'backordered';
	public const TYPE_EXCEPT = 'except';
	public const TYPE_REJECT = 'reject';
	public const TYPE_REQUESTTOPAY = 'requestToPay';
	public const TYPE_REPLACE = 'replace';

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("type")
	 */
	private string $type;

	/**
	 * @Ser\XmlAttribute
	 */
	private \DateTime $noticeDate;

	public function __construct(string $type, \DateTime $noticeDate = null)
	{
		Assertion::inArray($type, [
			self::TYPE_ACCEPT,
			self::TYPE_ALLDETAIL,
			self::TYPE_DETAIL,
			self::TYPE_BACKORDERED,
			self::TYPE_EXCEPT,
			self::TYPE_REJECT,
			self::TYPE_REQUESTTOPAY,
			self::TYPE_REPLACE,
		]);

		$this->type = $type;
		$this->noticeDate = $noticeDate ?? new \DateTime();
	}

	public static function create(string $type, \DateTime $noticeDate = null): self
	{
		return new static(
			$type,
			$noticeDate
		);
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getNoticeDate(): \DateTime
	{
		return $this->noticeDate;
	}
}
