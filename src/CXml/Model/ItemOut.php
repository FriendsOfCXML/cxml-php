<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemOut
{
    #[Ser\XmlAttribute]
    #[Ser\SerializedName('lineNumber')]
    private int $lineNumber;

    #[Ser\XmlAttribute]
    #[Ser\SerializedName('quantity')]
    private int $quantity;

    #[Ser\XmlAttribute]
    #[Ser\SerializedName('requestedDeliveryDate')]
    private ?\DateTimeInterface $requestedDeliveryDate = null;

    #[Ser\XmlAttribute]
    #[Ser\SerializedName('parentLineNumber')]
    private ?int $parentLineNumber = null;

    #[Ser\SerializedName('ItemID')]
    private ItemId $itemId;

    #[Ser\SerializedName('ItemDetail')]
    private ItemDetail $itemDetail;

    private function __construct(
        int $lineNumber,
        int $quantity,
        ItemId $itemId,
        ItemDetail $itemDetail,
        \DateTimeInterface $requestedDeliveryDate = null,
        int $parentLineNumber = null
    ) {
        $this->lineNumber = $lineNumber;
        $this->quantity = $quantity;
        $this->itemId = $itemId;
        $this->itemDetail = $itemDetail;
        $this->requestedDeliveryDate = $requestedDeliveryDate;
        $this->parentLineNumber = $parentLineNumber;
    }

    public static function create(
        int $lineNumber,
        int $quantity,
        ItemId $itemId,
        ItemDetail $itemDetail,
        \DateTimeInterface $requestedDeliveryDate = null,
        int $parentLineNumber = null
    ): self {
        return new self(
            $lineNumber,
            $quantity,
            $itemId,
            $itemDetail,
            $requestedDeliveryDate,
            $parentLineNumber
        );
    }

    public function addClassification(string $domain, string $value): self
    {
        $classification = new Classification($domain, $value);
        $this->itemDetail->addClassification($classification);

        return $this;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getRequestedDeliveryDate(): ?\DateTimeInterface
    {
        return $this->requestedDeliveryDate;
    }

    public function getItemId(): ItemId
    {
        return $this->itemId;
    }

    public function getItemDetail(): ItemDetail
    {
        return $this->itemDetail;
    }

    public function getParentLineNumber(): ?int
    {
        return $this->parentLineNumber;
    }
}
