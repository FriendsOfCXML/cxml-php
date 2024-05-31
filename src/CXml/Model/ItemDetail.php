<?php

namespace CXml\Model;

use Assert\Assertion;
use JMS\Serializer\Annotation as Ser;

class ItemDetail
{
    use ExtrinsicsTrait;
    public const UNIT_OF_MEASURE_EACH = 'EA';

    #[Ser\SerializedName('UnitPrice')]
    private MoneyWrapper $unitPrice;

    #[Ser\SerializedName('Description')]
    #[Ser\XmlElement(cdata: false)]
    private Description $description;

    #[Ser\SerializedName('UnitOfMeasure')]
    #[Ser\XmlElement(cdata: false)]
    private string $unitOfMeasure;

    /**
     *
     * @var Classification[]
     */
    #[Ser\XmlList(inline: true, entry: 'Classification')]
    #[Ser\Type('array<CXml\Model\Classification>')]
    private array $classifications = [];

    #[Ser\SerializedName('ManufacturerPartID')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $manufacturerPartId = null;

    #[Ser\SerializedName('ManufacturerName')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $manufacturerName = null;

    #[Ser\SerializedName('URL')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $url = null;

    #[Ser\SerializedName('LeadTime')]
    #[Ser\XmlElement(cdata: false)]
    private ?int $leadtime = null;

    protected function __construct(Description $description, string $unitOfMeasure, MoneyWrapper $unitPrice)
    {
        $this->description = $description;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->unitPrice = $unitPrice;
    }

    public static function create(Description $description, string $unitOfMeasure, MoneyWrapper $unitPrice, array $classifications): self
    {
        Assertion::allIsInstanceOf($classifications, Classification::class);
        Assertion::notEmpty($classifications); // at least one classification is necessary (via DTD)

        $itemDetail = new self($description, $unitOfMeasure, $unitPrice);

        foreach ($classifications as $classification) {
            $itemDetail->addClassification($classification);
        }

        return $itemDetail;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function setLeadtime(?int $leadtime): self
    {
        $this->leadtime = $leadtime;

        return $this;
    }

    public function setManufacturerPartId(?string $manufacturerPartId): self
    {
        $this->manufacturerPartId = $manufacturerPartId;

        return $this;
    }

    public function setManufacturerName(?string $manufacturerName): self
    {
        $this->manufacturerName = $manufacturerName;

        return $this;
    }

    public function addClassification(Classification $classification): self
    {
        $this->classifications[] = $classification;

        return $this;
    }

    public function getUnitPrice(): MoneyWrapper
    {
        return $this->unitPrice;
    }

    public function getDescription(): Description
    {
        return $this->description;
    }

    public function getUnitOfMeasure(): string
    {
        return $this->unitOfMeasure;
    }

    public function getClassifications(): array
    {
        return $this->classifications;
    }

    public function getManufacturerPartId(): ?string
    {
        return $this->manufacturerPartId;
    }

    public function getManufacturerName(): ?string
    {
        return $this->manufacturerName;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getLeadtime(): ?int
    {
        return $this->leadtime;
    }
}
