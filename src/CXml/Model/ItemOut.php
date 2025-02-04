<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\CommentsTrait;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['itemId', 'itemDetail', 'shipTo', 'distribution', 'controlKeys', 'scheduleLine'])]
class ItemOut
{
    use CommentsTrait;

    private function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('lineNumber')]
        public readonly int $lineNumber,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        public readonly int $quantity,
        #[Serializer\SerializedName('ItemID')]
        public readonly ItemId $itemId,
        #[Serializer\SerializedName('ItemDetail')]
        public readonly ItemDetail $itemDetail,
        #[Serializer\XmlAttribute]
        public readonly ?DateTimeInterface $requestedDeliveryDate = null,
        #[Serializer\XmlAttribute]
        public readonly ?int $parentLineNumber = null,
        #[Serializer\SerializedName('ShipTo')]
        public readonly ?ShipTo $shipTo = null,
        #[Serializer\SerializedName('Distribution')]
        public readonly ?Distribution $distribution = null,
        #[Serializer\SerializedName('ControlKeys')]
        public readonly ?ControlKeys $controlKeys = null,
        #[Serializer\SerializedName('ScheduleLine')]
        public readonly ?ScheduleLine $scheduleLine = null,
    ) {
    }

    public static function create(
        int $lineNumber,
        int $quantity,
        ItemId $itemId,
        ItemDetail $itemDetail,
        ?DateTimeInterface $requestedDeliveryDate = null,
        ?int $parentLineNumber = null,
    ): self {
        return new self(
            $lineNumber,
            $quantity,
            $itemId,
            $itemDetail,
            $requestedDeliveryDate,
            $parentLineNumber,
        );
    }

    public function addClassification(string $domain, string $value): self
    {
        $classification = new Classification($domain, $value);
        $this->itemDetail->addClassification($classification);

        return $this;
    }
}
