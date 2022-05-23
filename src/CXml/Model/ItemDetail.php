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
	private $description;

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
	private ?string $manufacturerPartId;

	/**
	 * @Ser\SerializedName("ManufacturerName")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $manufacturerName;

	/**
	 * @Ser\SerializedName("URL")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?string $url;

	public function __construct(/* MultilanguageString|MultilanguageShortName */ $description, string $unitOfMeasure, MoneyWrapper $unitPrice, ?string $manufacturerPartId = null, ?string $manufacturerName = null, ?string $url = null)
	{
		$this->description = $description;
		$this->unitOfMeasure = $unitOfMeasure;
		$this->unitPrice = $unitPrice;
		$this->manufacturerPartId = $manufacturerPartId;
		$this->manufacturerName = $manufacturerName;
		$this->url = $url;
	}

	public function addClassification(Classification $classification): self
	{
		$this->classifications[] = $classification;

		return $this;
	}
}
