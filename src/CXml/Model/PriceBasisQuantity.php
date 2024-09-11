<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class PriceBasisQuantity
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        private int $quantity,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('conversionFactor')]
        private float $conversionFactor,
        #[Serializer\SerializedName('UnitOfMeasure')]
        #[Serializer\XmlElement(cdata: false)]
        private string $unitOfMeasure,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        private MultilanguageString $description,
    ) {
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getConversionFactor(): float
    {
        return $this->conversionFactor;
    }

    public function getUnitOfMeasure(): string
    {
        return $this->unitOfMeasure;
    }

    public function getDescription(): MultilanguageString
    {
        return $this->description;
    }
}
