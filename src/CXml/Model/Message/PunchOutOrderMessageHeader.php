<?php

namespace CXml\Model\Message;

use CXml\Model\MoneyWrapper;
use CXml\Model\SupplierOrderInfo;
use JMS\Serializer\Annotation as Ser;

class PunchOutOrderMessageHeader
{
	public const OPERATION_CREATE = 'create';
	public const OPERATION_EDIT = 'edit';
	public const OPERATION_INSPECT = 'inspect';

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $operationAllowed = null;

	/**
	 * @Ser\SerializedName("Total")
	 */
	private MoneyWrapper $total;

	/**
	 * @Ser\SerializedName("Shipping")
	 */
	private ?MoneyWrapper $shipping = null;

	/**
	 * @Ser\SerializedName("Tax")
	 */
	private ?MoneyWrapper $tax = null;

	/**
	 * @Ser\SerializedName("SupplierOrderInfo")
	 */
	private ?SupplierOrderInfo $supplierOrderInfo = null;

	public function __construct(MoneyWrapper $total, ?MoneyWrapper $shipping = null, ?MoneyWrapper $tax = null, ?string $operationAllowed = null)
	{
		$this->total = $total;
		$this->shipping = $shipping;
		$this->tax = $tax;
		$this->operationAllowed = $operationAllowed ?? self::OPERATION_CREATE;
	}

	public function setSupplierOrderInfo(string $orderId, \DateTime $orderDate = null): self
	{
		$this->supplierOrderInfo = new SupplierOrderInfo($orderId, $orderDate);

		return $this;
	}
}
