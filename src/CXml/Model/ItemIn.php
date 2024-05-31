<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['itemId', 'itemDetail'])]
class ItemIn
{
    protected function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        private readonly int $quantity,
        #[Serializer\SerializedName('ItemID')]
        private readonly ?ItemId $itemId,
        #[Serializer\SerializedName('ItemDetail')]
        private readonly ItemDetail $itemDetail
    ) {
    }

    public static function create(
        int $quantity,
        ItemId $itemId,
        ItemDetail $itemDetail
    ): self {
        return new self(
            $quantity,
            $itemId,
            $itemDetail,
        );
    }

    public function addClassification(string $domain, string $value): self
    {
        $classification = new Classification($domain, $value);
        $this->itemDetail->addClassification($classification);

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getItemId(): ?ItemId
    {
        return $this->itemId;
    }

    public function getItemDetail(): ItemDetail
    {
        return $this->itemDetail;
    }
}
