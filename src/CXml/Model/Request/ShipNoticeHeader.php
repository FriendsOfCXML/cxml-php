<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use CXml\Model\CommentsTrait;
use CXml\Model\DocumentReference;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReferencesTrait;
use DateTime;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['documentReference', 'comments', 'extrinsics', 'idReferences'])]
class ShipNoticeHeader
{
    use ExtrinsicsTrait;
    use IdReferencesTrait;
    use CommentsTrait;

    #[Serializer\XmlAttribute]
    private DateTimeInterface $noticeDate;

    #[Serializer\SerializedName('DocumentReference')]
    private ?DocumentReference $documentReference;

    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('shipmentID')]
        private readonly string $shipmentId,
        DateTimeInterface $noticeDate = null,
        #[Serializer\XmlAttribute]
        private readonly ?DateTimeInterface $shipmentDate = null,
        #[Serializer\XmlAttribute]
        private readonly ?DateTimeInterface $deliveryDate = null,
        string $documentReference = null,
    ) {
        $this->noticeDate = $noticeDate ?? new DateTime();
        $this->documentReference = null !== $documentReference && '' !== $documentReference && '0' !== $documentReference ? new DocumentReference($documentReference) : null;
    }

    public static function create(string $shipmentId, DateTimeInterface $noticeDate = null, DateTimeInterface $shipmentDate = null, DateTimeInterface $deliveryDate = null, string $documentReference = null): self
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

    public function getNoticeDate(): DateTimeInterface
    {
        return $this->noticeDate;
    }

    public function getShipmentDate(): ?DateTimeInterface
    {
        return $this->shipmentDate;
    }

    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }
}
