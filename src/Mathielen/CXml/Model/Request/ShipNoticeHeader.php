<?php

namespace Mathielen\CXml\Model\Request;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Comment;
use Mathielen\CXml\Model\DocumentReference;
use Mathielen\CXml\Model\Extrinsic;

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
	private ?\DateTime $shipmentDate;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?\DateTime $deliveryDate;

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
	private ?array $comments;

	/**
	 * @Ser\XmlList(inline=true, entry="Extrinsic")
	 * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
	 *
	 * @var Extrinsic[]
	 */
	private array $extrinsics = [];

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

	public function addComment(string $comment, ?string $lang = null): self
	{
		$this->comments[] = new Comment($comment, $lang);

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
}
