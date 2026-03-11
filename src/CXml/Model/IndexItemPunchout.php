<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class IndexItemPunchout implements IndexItemInterface
{
    public function __construct(
        #[Serializer\SerializedName('ItemID')]
        public ItemId $itemId,
        #[Serializer\SerializedName('ItemDetail')]
        public ItemDetail $itemDetail,
        #[Serializer\SerializedName('IndexItemDetail')]
        public IndexItemDetail $indexItemDetail,
    ) {
    }

    public static function create(ItemId $itemId, ItemDetail $itemDetail, IndexItemDetail $indexItemDetail): self
    {
        return new self($itemId, $itemDetail, $indexItemDetail);
    }
}
