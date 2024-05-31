<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class InventoryQuantity
{
    #[Serializer\SerializedName('UnitOfMeasure')]
    private UnitOfMeasure $unitOfMeasure;

    public function __construct(
        #[Serializer\XmlAttribute]
        private int $quantity,
        string $unitOfMeasure
    ) {
        $this->unitOfMeasure = new UnitOfMeasure($unitOfMeasure);
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitOfMeasure(): string
    {
        return $this->unitOfMeasure->getValue();
    }
}
