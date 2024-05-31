<?php

namespace CXml\Model\Request;

use CXml\Model\CommentsTrait;
use CXml\Model\DocumentReference;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

class ShipNoticeHeader
{
    use ExtrinsicsTrait;
    use IdReferencesTrait;
    use CommentsTrait;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('shipmentID')]
    private string $shipmentId;

    #[Serializer\XmlAttribute]
    private \DateTimeInterface $noticeDate;

    #[Serializer\XmlAttribute]
    private ?\DateTimeInterface $shipmentDate = null;

    #[Serializer\XmlAttribute]
    private ?\DateTimeInterface $deliveryDate = null;

    #[Serializer\SerializedName('DocumentReference')]
    private ?DocumentReference $documentReference = null;

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
