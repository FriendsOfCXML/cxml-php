<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class OrderReference
{
	/**
	 * @Ser\SerializedName("DocumentReference")
	 */
	private ?DocumentReference $documentReference = null;

	/**
	 * @Ser\SerializedName("orderID")
	 * @Ser\XmlAttribute
	 */
	private ?string $orderId = null;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?\DateTime $orderDate = null;

	public function __construct(DocumentReference $documentReference, ?string $orderId = null, ?\DateTime $orderDate = null)
	{
		$this->documentReference = $documentReference;
		$this->orderId = $orderId;
		$this->orderDate = $orderDate;
	}

	public static function create(string $documentReference): self
	{
		return new static(
			new DocumentReference($documentReference)
		);
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
