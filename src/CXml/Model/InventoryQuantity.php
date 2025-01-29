<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class InventoryQuantity
{
    #[Serializer\SerializedName('UnitOfMeasure')]
    public UnitOfMeasure $unitOfMeasure;

    public function __construct(
        #[Serializer\XmlAttribute]
        public int $quantity,
        string $unitOfMeasure,
    ) {
        $this->unitOfMeasure = new UnitOfMeasure($unitOfMeasure);
    }
}
