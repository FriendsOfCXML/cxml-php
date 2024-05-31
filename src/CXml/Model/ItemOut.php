<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class ItemOut
{
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('lineNumber')]
    private int $lineNumber;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('quantity')]
    private int $quantity;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('requestedDeliveryDate')]
    private ?\DateTimeInterface $requestedDeliveryDate = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('parentLineNumber')]
    private ?int $parentLineNumber = null;

    #[Serializer\SerializedName('ItemID')]
    private ItemId $itemId;

    #[Serializer\SerializedName('ItemDetail')]
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
