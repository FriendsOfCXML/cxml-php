<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class SelectedItem
{
    #[Serializer\SerializedName('ItemID')]
    private ItemId $itemId;

    public function __construct(ItemId $itemId)
    {
        $this->itemId = $itemId;
    }

    public function getItemId(): ItemId
    {
        return $this->itemId;
    }
}
