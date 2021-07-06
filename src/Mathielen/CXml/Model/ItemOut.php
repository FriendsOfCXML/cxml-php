<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemOut
{
    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("lineNumber")
     */
    private int $lineNumber;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("quantity")
     */
    private int $quantity;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("requestedDeliveryDate")
     * @Ser\Type("DateTime<'Y-m-d'>")
     */
    private \DateTime $requestedDeliveryDate;

    /**
     * @Ser\SerializedName("ItemID")
     */
    private ItemId $itemID;

    /**
     * @Ser\SerializedName("ItemDetail")
     */
    private ItemDetail $itemDetail;

    private function __construct(int $lineNumber, int $quantity, \DateTime $requestedDeliveryDate, ItemId $itemID, ItemDetail $itemDetail)
    {
        $this->lineNumber = $lineNumber;
        $this->quantity = $quantity;
        $this->requestedDeliveryDate = $requestedDeliveryDate;
        $this->itemID = $itemID;
        $this->itemDetail = $itemDetail;
    }

    public static function create(int $lineNumber, int $quantity, \DateTime $requestedDeliveryDate, ItemId $itemID, ItemDetail $itemDetail): self
    {
        return new self(
            $lineNumber,
            $quantity,
            $requestedDeliveryDate,
            $itemID,
            $itemDetail,
        );
    }

    public function addClassification(string $domain, string $value): self
    {
        $classification = new Classification($domain, $value);
        $this->itemDetail->addClassification($classification);

        return $this;
    }
}
