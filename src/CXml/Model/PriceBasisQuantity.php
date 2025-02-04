<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class PriceBasisQuantity
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        public int $quantity,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('conversionFactor')]
        public float $conversionFactor,
        #[Serializer\SerializedName('UnitOfMeasure')]
        #[Serializer\XmlElement(cdata: false)]
        public string $unitOfMeasure,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        public MultilanguageString $description,
    ) {
    }
}
