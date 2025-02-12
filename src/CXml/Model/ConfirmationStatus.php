<?php

declare(strict_types=1);

namespace CXml\Model;

use Assert\Assertion;
use CXml\Model\Request\ConfirmationHeader;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['quantity', 'type', 'shipmentDate', 'deliveryDate', 'unitOfMeasure', 'itemIn'])]
class ConfirmationStatus
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        public readonly int $quantity,
        #[Serializer\SerializedName('type')]
        #[Serializer\XmlAttribute]
        public readonly string $type,
        #[Serializer\SerializedName('UnitOfMeasure')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly string $unitOfMeasure,
        #[Serializer\SerializedName('ItemIn')]
        #[Serializer\XmlElement(cdata: false)]
        private ItemIn $itemIn,
        #[Serializer\XmlAttribute]
        public readonly ?DateTimeInterface $shipmentDate = null,
        #[Serializer\XmlAttribute]
        public readonly ?DateTimeInterface $deliveryDate = null,
    ) {
        Assertion::inArray($type, [
            ConfirmationHeader::TYPE_ACCEPT,
            ConfirmationHeader::TYPE_ALLDETAIL,
            ConfirmationHeader::TYPE_DETAIL,
            ConfirmationHeader::TYPE_BACKORDERED,
            ConfirmationHeader::TYPE_REJECT,
            ConfirmationHeader::TYPE_REQUESTTOPAY,
            ConfirmationHeader::TYPE_UNKNOWN,
        ]);
    }
}
