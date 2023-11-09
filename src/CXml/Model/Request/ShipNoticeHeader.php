<?php

namespace CXml\Model\Request;

use CXml\Model\Comment;
use CXml\Model\DocumentReference;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReferencesTrait;
use JMS\Serializer\Annotation as Ser;

class ShipNoticeHeader
{
    use ExtrinsicsTrait;
    use IdReferencesTrait;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("shipmentID")
     */
    private string $shipmentId;

    /**
     * @Ser\XmlAttribute
     */
    private \DateTimeInterface $noticeDate;

    /**
     * @Ser\XmlAttribute
     */
    private ?\DateTimeInterface $shipmentDate = null;

    /**
     * @Ser\XmlAttribute
     */
    private ?\DateTimeInterface $deliveryDate = null;

    /**
     * @Ser\SerializedName("DocumentReference")
     */
    private ?DocumentReference $documentReference = null;

    /**
     * @Ser\XmlList(inline=true, entry="Comments")
     * @Ser\Type("array<CXml\Model\Comment>")
     *
     * @var Comment[]
     */
    private ?array $comments = null;

    public function __construct(string $shipmentId, \DateTimeInterface $noticeDate = null, \DateTimeInterface $shipmentDate = null, \DateTimeInterface $deliveryDate = null, string $documentReference = null)
    {
        $this->shipmentId = $shipmentId;
        $this->noticeDate = $noticeDate ?? new \DateTime();
        $this->shipmentDate = $shipmentDate;
        $this->deliveryDate = $deliveryDate;
        $this->documentReference = $documentReference ? new DocumentReference($documentReference) : null;
    }

    public static function create(string $shipmentId, \DateTimeInterface $noticeDate = null, \DateTimeInterface $shipmentDate = null, \DateTimeInterface $deliveryDate = null, string $documentReference = null): self
    {
        return new self($shipmentId, $noticeDate, $shipmentDate, $deliveryDate, $documentReference);
    }

    public function getDocumentReference(): ?DocumentReference
    {
        return $this->documentReference;
    }

    public function addComment(string $comment, string $type = null, string $lang = null): self
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

    public function getNoticeDate(): \DateTimeInterface
    {
        return $this->noticeDate;
    }

    public function getShipmentDate(): ?\DateTimeInterface
    {
        return $this->shipmentDate;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }
}
