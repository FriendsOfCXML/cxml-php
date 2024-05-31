<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class SupplierOrderInfo
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('orderID')]
        private string $orderId,
        #[Serializer\XmlAttribute]
        private ?\DateTimeInterface $orderDate = null
    ) {
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }
}
