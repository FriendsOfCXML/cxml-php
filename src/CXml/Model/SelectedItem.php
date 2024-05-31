<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['ItemID'])]
readonly class SelectedItem
{
    public function __construct(
        #[Serializer\SerializedName('ItemID')]
        private ItemId $itemId
    ) {
    }

    public function getItemId(): ItemId
    {
        return $this->itemId;
    }
}
