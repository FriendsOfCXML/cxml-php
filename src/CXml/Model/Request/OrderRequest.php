<?php

namespace CXml\Model\Request;

use CXml\Model\ItemOut;
use JMS\Serializer\Annotation as Ser;

class OrderRequest implements RequestPayloadInterface
{
	/**
	 * @Ser\SerializedName("OrderRequestHeader")
	 */
	private OrderRequestHeader $orderRequestHeader;

	/**
	 * @Ser\XmlList(inline=true, entry="ItemOut")
	 * @Ser\Type("array<CXml\Model\ItemOut>")
	 *
	 * @var ItemOut[]
	 */
	private array $itemOut = [];

	protected function __construct(OrderRequestHeader $orderRequestHeader)
	{
		$this->orderRequestHeader = $orderRequestHeader;
	}

	public static function create(OrderRequestHeader $orderRequestHeader): self
	{
		return new self(
			$orderRequestHeader
		);
	}

	public function addItems(array $items): self
	{
		foreach ($items as $item) {
			$this->addItem($item);
		}

		return $this;
	}

	public function addItem(ItemOut $item): self
	{
		$this->itemOut[] = $item;

		return $this;
	}

	public function getOrderRequestHeader(): OrderRequestHeader
	{
		return $this->orderRequestHeader;
	}

	public function getItems(): array
	{
		return $this->itemOut;
	}
}
