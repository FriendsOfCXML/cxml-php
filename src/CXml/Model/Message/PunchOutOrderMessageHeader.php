<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use Assert\Assertion;
use CXml\Model\MoneyWrapper;
use CXml\Model\Shipping;
use CXml\Model\ShipTo;
use CXml\Model\SupplierOrderInfo;
use CXml\Model\Tax;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['sourcingStatus', 'total', 'shipTo', 'shipping', 'tax', 'supplierOrderInfo'])]
class PunchOutOrderMessageHeader
{
    final public const OPERATION_CREATE = 'create';

    final public const OPERATION_EDIT = 'edit';

    final public const OPERATION_INSPECT = 'inspect';

    #[Serializer\XmlAttribute]
    private ?string $operationAllowed = null; /* cant be 'readonly' bc must be initialized with null -> jms deserialization */

    #[Serializer\SerializedName('ShipTo')]
    private ?ShipTo $shipTo = null;

    #[Serializer\SerializedName('SupplierOrderInfo')]
    private ?SupplierOrderInfo $supplierOrderInfo = null;

    public function __construct(
        #[Serializer\SerializedName('Total')]
        public readonly MoneyWrapper $total,
        #[Serializer\SerializedName('Shipping')]
        public readonly ?Shipping $shipping = null,
        #[Serializer\SerializedName('Tax')]
        public readonly ?Tax $tax = null,
        ?string $operationAllowed = null,
    ) {
        Assertion::inArray($operationAllowed, [self::OPERATION_CREATE, self::OPERATION_EDIT, self::OPERATION_INSPECT, null]);
        $this->operationAllowed = $operationAllowed ?? self::OPERATION_CREATE;
    }

    public function setShipTo(?ShipTo $shipTo): self
    {
        $this->shipTo = $shipTo;

        return $this;
    }

    public function getShipTo(): ?ShipTo
    {
        return $this->shipTo;
    }

    public function setSupplierOrderInfo(string $orderId, ?DateTimeInterface $orderDate = null): self
    {
        $this->supplierOrderInfo = new SupplierOrderInfo($orderId, $orderDate);

        return $this;
    }

    public function getOperationAllowed(): ?string
    {
        return $this->operationAllowed;
    }

    public function getSupplierOrderInfo(): ?SupplierOrderInfo
    {
        return $this->supplierOrderInfo;
    }
}
