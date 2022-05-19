<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ItemOut
{
	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("lineNumber")
	 */
	private int $lineNumber;

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("quantity")
	 */
	private int $quantity;

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("requestedDeliveryDate")
	 * @Ser\Type("DateTime<'Y-m-d'>")
	 */
	private ?\DateTime $requestedDeliveryDate;

	/**
	 * @Ser\SerializedName("ItemID")
	 */
	private ItemId $itemId;

	/**
	 * @Ser\SerializedName("ItemDetail")
	 */
	private ItemDetail $itemDetail;

	private function __construct(
		int $lineNumber,
		int $quantity,
		ItemId $itemId,
		ItemDetail $itemDetail,
		?\DateTime $requestedDeliveryDate = null
	) {
		$this->lineNumber = $lineNumber;
		$this->quantity = $quantity;
		$this->itemId = $itemId;
		$this->itemDetail = $itemDetail;
		$this->requestedDeliveryDate = $requestedDeliveryDate;
	}

	public static function create(
		int $lineNumber,
		int $quantity,
		ItemId $itemId,
		ItemDetail $itemDetail,
		?\DateTime $requestedDeliveryDate = null
	): self {
		return new self(
			$lineNumber,
			$quantity,
			$itemId,
			$itemDetail,
			$requestedDeliveryDate,
		);
	}

	public function addClassification(string $domain, string $value): self
	{
		$classification = new Classification($domain, $value);
		$this->itemDetail->addClassification($classification);

		return $this;
	}
}
