<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['ItemID'])]
readonly class SelectedItem
{
    public function __construct(
        #[Serializer\SerializedName('ItemID')]
        public ItemId $itemId,
    ) {
    }
}
