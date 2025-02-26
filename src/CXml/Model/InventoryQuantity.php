<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class InventoryQuantity
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public int $quantity,
        #[Serializer\SerializedName('UnitOfMeasure')]
        #[Serializer\XmlElement(cdata: false)]
        public string $unitOfMeasure,
    ) {
    }
}
