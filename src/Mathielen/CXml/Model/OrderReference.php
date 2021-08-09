<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class OrderReference
{
	/**
	 * @Ser\SerializedName("DocumentReference")
	 */
	private ?DocumentReference $documentReference;

	/**
	 * @Ser\SerializedName("orderID")
	 * @Ser\XmlAttribute
	 */
	private ?string $orderId;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?\DateTime $orderDate;

	public function __construct(DocumentReference $documentReference, ?string $orderId = null, ?\DateTime $orderDate = null)
	{
		$this->documentReference = $documentReference;
		$this->orderId = $orderId;
		$this->orderDate = $orderDate;
	}

	public function getDocumentReference(): ?DocumentReference
	{
		return $this->documentReference;
	}

	public function getOrderId(): ?string
	{
		return $this->orderId;
	}

	public function getOrderDate(): ?\DateTime
	{
		return $this->orderDate;
	}
}
