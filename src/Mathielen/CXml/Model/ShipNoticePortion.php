<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class ShipNoticePortion
{
	/**
	 * @Ser\SerializedName("OrderReference")
	 */
	private OrderReference $orderReference;

	public function __construct(string $documentReference, string $orderId, ?\DateTime $orderDate = null)
	{
		$this->orderReference = new OrderReference(
			new DocumentReference(
				$documentReference
			),
			$orderId,
			$orderDate
		);
	}

	public function getOrderReference(): OrderReference
	{
		return $this->orderReference;
	}
}
