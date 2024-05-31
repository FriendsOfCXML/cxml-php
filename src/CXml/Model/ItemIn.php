<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class ItemIn
{
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('quantity')]
    private int $quantity;

    #[Serializer\SerializedName('ItemID')]
    private ?ItemId $itemId = null; // might be used in a quote, therefore can be null
    #[Serializer\SerializedName('ItemDetail')]
    private ItemDetail $itemDetail;

    protected function __construct(
        int $quantity,
        ItemId $itemId,
        ItemDetail $itemDetail
    ) {
        $this->quantity = $quantity;
        $this->itemId = $itemId;
        $this->itemDetail = $itemDetail;
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
