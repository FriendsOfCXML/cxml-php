<?php

namespace Mathielen\CXml\Model\Request;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\IdReference;

class ConfirmationHeader
{

	const TYPE_ACCEPT = 'accept';
	const TYPE_ALLDETAIL = 'allDetail';
	const TYPE_DETAIL = 'detail';
	const TYPE_BACKORDERED = 'backordered';
	const TYPE_EXCEPT = 'except';
	const TYPE_REJECT = 'reject';
	const TYPE_REQUESTTOPAY = 'requestToPay';
	const TYPE_REPLACE = 'replace';

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
	 * @Ser\Type("array<Mathielen\CXml\Model\IdReference>")
	 *
	 * @var IdReference[]
	 */
	private array $idReferences = [];

	/**
	 * @Ser\XmlList(inline=true, entry="Extrinsic")
	 * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
	 *
	 * @var Extrinsic[]
	 */
	private array $extrinsics = [];

	public function __construct(string $type, \DateTime $noticeDate = null)
	{
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

	public function addExtrinsic(Extrinsic $extrinsic): self
	{
		$this->extrinsics[] = $extrinsic;

		return $this;
	}

	public function getExtrinsics(): array
	{
		return $this->extrinsics;
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
