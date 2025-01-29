<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['itemId', 'itemDetail'])]
readonly class ItemIn
{
    protected function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        public int $quantity,
        #[Serializer\SerializedName('ItemID')]
        public ?ItemId $itemId,
        #[Serializer\SerializedName('ItemDetail')]
        public ItemDetail $itemDetail,
    ) {
    }

    public static function create(
        int $quantity,
        ItemId $itemId,
        ItemDetail $itemDetail,
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
}
