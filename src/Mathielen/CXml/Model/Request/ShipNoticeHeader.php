<?php

namespace Mathielen\CXml\Model\Request;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Comment;
use Mathielen\CXml\Model\DocumentReference;
use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\IdReference;

class ShipNoticeHeader
{
	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("shipmentID")
	 */
	private string $shipmentId;

	/**
	 * @Ser\XmlAttribute
	 */
	private \DateTime $noticeDate;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?\DateTime $shipmentDate = null;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?\DateTime $deliveryDate = null;

	/**
	 * @Ser\SerializedName("DocumentReference")
	 */
	private ?DocumentReference $documentReference = null;

	/**
	 * @Ser\XmlList(inline=true, entry="Comments")
	 * @Ser\Type("array<Mathielen\CXml\Model\Comment>")
	 *
	 * @var Comment[]
	 */
	private ?array $comments = null;

	/**
	 * @Ser\XmlList(inline=true, entry="Extrinsic")
	 * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
	 *
	 * @var Extrinsic[]
	 */
	private array $extrinsics = [];

	/**
	 * @Ser\XmlList(inline=true, entry="IdReference")
	 * @Ser\Type("array<Mathielen\CXml\Model\IdReference>")
	 *
	 * @var IdReference[]
	 */
	private array $idReferences = [];

	public function __construct(string $shipmentId, ?\DateTime $noticeDate = null, ?\DateTime $shipmentDate = null, ?\DateTime $deliveryDate = null, string $documentReference = null)
	{
		$this->shipmentId = $shipmentId;
		$this->noticeDate = $noticeDate ?? new \DateTime();
		$this->shipmentDate = $shipmentDate;
		$this->deliveryDate = $deliveryDate;
		$this->documentReference = $documentReference ? new DocumentReference($documentReference) : null;
	}

	public static function create(string $shipmentId, ?\DateTime $noticeDate = null, ?\DateTime $shipmentDate = null, ?\DateTime $deliveryDate = null, string $documentReference = null): self
	{
		return new self($shipmentId, $noticeDate, $shipmentDate, $deliveryDate, $documentReference);
	}

	public function getDocumentReference(): ?DocumentReference
	{
		return $this->documentReference;
	}

	public function addComment(string $comment, ?string $type = null, ?string $lang = null): self
	{
		if (null === $this->comments) {
			$this->comments = [];
		}

		$this->comments[] = new Comment($comment, $type, $lang);

		return $this;
	}

	public function getComments(): ?array
	{
		return $this->comments;
	}

	public function getShipmentId(): string
	{
		return $this->shipmentId;
	}

	public function getNoticeDate(): \DateTime
	{
		return $this->noticeDate;
	}

	public function getShipmentDate(): ?\DateTime
	{
		return $this->shipmentDate;
	}

	public function getDeliveryDate(): ?\DateTime
	{
		return $this->deliveryDate;
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
