<?php

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReference;
use JMS\Serializer\Annotation as Ser;

class ConfirmationHeader
{
	use ExtrinsicsTrait;
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

	/**
	 * @Ser\XmlList(inline=true, entry="IdReference")
	 * @Ser\Type("array<CXml\Model\IdReference>")
	 *
	 * @var IdReference[]
	 */
	private array $idReferences = [];

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
		return new self(
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

	public function addIdReference(string $domain, string $identifier): self
	{
		$this->idReferences[] = new IdReference($domain, $identifier);

		return $this;
	}

	public function getIdReferences(): array
	{
		return $this->idReferences;
	}

	public function getIdReference(string $domain): ?string
	{
		foreach ($this->idReferences as $idReference) {
			if ($idReference->getDomain() === $domain) {
				return $idReference->getIdentifier();
			}
		}

		return null;
	}
}
