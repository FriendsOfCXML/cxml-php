<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemIn
{
    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("quantity")
     */
    private int $quantity;

    /**
     * @Ser\SerializedName("ItemID")
     */
    private ItemId $itemId;

    /**
     * @Ser\SerializedName("ItemDetail")
     */
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

    public function getItemId(): ItemId
    {
        return $this->itemId;
    }

    public function getItemDetail(): ItemDetail
    {
        return $this->itemDetail;
    }
}
