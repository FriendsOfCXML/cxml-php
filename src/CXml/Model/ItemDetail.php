<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemDetail
{
	public const UNIT_OF_MEASURE_EACH = 'EA';

	/**
	 * @Ser\SerializedName("UnitPrice")
	 */
	private MoneyWrapper $unitPrice;

	/**
	 * @Ser\SerializedName("Description")
	 * @Ser\XmlElement (cdata=false)
	 */
	private Description $description;

	/**
	 * @Ser\SerializedName("UnitOfMeasure")
	 * @Ser\XmlElement (cdata=false)
	 */
	private string $unitOfMeasure;

	/**
	 * @Ser\XmlList(inline=true, entry="Classification")
	 * @Ser\Type("array<CXml\Model\Classification>")
	 *
	 * @var Classification[]
	 */
	private array $classifications = [];

	/**
	 * @Ser\SerializedName("ManufacturerPartID")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $manufacturerPartId = null;

	/**
	 * @Ser\SerializedName("ManufacturerName")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $manufacturerName = null;

	/**
	 * @Ser\SerializedName("URL")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $url = null;

	/**
	 * @Ser\SerializedName("LeadTime")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?int $leadtime = null;

	private function __construct(Description $description, string $unitOfMeasure, MoneyWrapper $unitPrice)
	{
		$this->description = $description;
		$this->unitOfMeasure = $unitOfMeasure;
		$this->unitPrice = $unitPrice;
	}

	public static function create(Description $description, string $unitOfMeasure, MoneyWrapper $unitPrice): self
	{
		return new self($description, $unitOfMeasure, $unitPrice);
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
}
