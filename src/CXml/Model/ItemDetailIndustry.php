<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class ItemDetailIndustry
{
    #[Serializer\SerializedName('ItemDetailRetail')]
    public ItemDetailRetail $itemDetailRetail;

    /**
     * @param Characteristic[] $characteristics
     */
    public function __construct(
        array $characteristics = [],
    ) {
        $this->itemDetailRetail = new ItemDetailRetail($characteristics);
    }
}
