<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class PriceBasisQuantity
{
    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("quantity")
     */
    private int $quantity;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("conversionFactor")
     */
    private float $conversionFactor;

    /**
     * @Ser\SerializedName("UnitOfMeasure")
     * @Ser\XmlElement (cdata=false)
     */
    private string $unitOfMeasure;

    /**
     * @Ser\SerializedName("Description")
     * @Ser\XmlElement (cdata=false)
     */
    private MultilanguageString $description;

    public function __construct(int $quantity, float $conversionFactor, string $unitOfMeasure, MultilanguageString $description)
    {
        $this->quantity = $quantity;
        $this->conversionFactor = $conversionFactor;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->description = $description;
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
