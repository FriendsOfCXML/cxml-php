<?php

declare(strict_types=1);

namespace CXml\Model;

use Assert\Assertion;
use CXml\Model\Trait\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['unitPrice', 'description', 'unitOfMeasure', 'priceBasisQuantity', 'classifications', 'manufacturerPartId', 'manufacturerName', 'url', 'leadtime'])]
class ItemDetail
{
    use ExtrinsicsTrait;

    final public const UNIT_OF_MEASURE_EACH = 'EA';

    /**
     * @var Classification[]
     */
    #[Serializer\XmlList(entry: 'Classification', inline: true)]
    #[Serializer\Type('array<CXml\Model\Classification>')]
    private array $classifications = [];

    #[Serializer\SerializedName('ManufacturerPartID')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $manufacturerPartId = null;

    #[Serializer\SerializedName('ManufacturerName')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $manufacturerName = null;

    #[Serializer\SerializedName('URL')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $url = null;

    #[Serializer\SerializedName('LeadTime')]
    #[Serializer\XmlElement(cdata: false)]
    private ?int $leadtime = null;

    protected function __construct(
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly Description $description,
        #[Serializer\SerializedName('UnitOfMeasure')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly string $unitOfMeasure,
        #[Serializer\SerializedName('UnitPrice')]
        public readonly MoneyWrapper $unitPrice,
        #[Serializer\SerializedName('PriceBasisQuantity')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly ?PriceBasisQuantity $priceBasisQuantity = null,
    ) {
    }

    public static function create(Description $description, string $unitOfMeasure, MoneyWrapper $unitPrice, array $classifications, ?PriceBasisQuantity $priceBasisQuantity = null): self
    {
        Assertion::allIsInstanceOf($classifications, Classification::class);
        Assertion::notEmpty($classifications); // at least one classification is necessary (via DTD)

        $itemDetail = new self($description, $unitOfMeasure, $unitPrice, $priceBasisQuantity);

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
