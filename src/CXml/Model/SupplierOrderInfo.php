<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class SupplierOrderInfo
{
	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("orderID")
	 */
	private string $orderId;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?\DateTime $orderDate;

	public function __construct(string $orderId, \DateTime $orderDate = null)
	{
		$this->orderId = $orderId;
		$this->orderDate = $orderDate;
	}

	public function getOrderId(): string
	{
		return $this->orderId;
	}

	public function getOrderDate(): ?\DateTime
	{
		return $this->orderDate;
	}
}
