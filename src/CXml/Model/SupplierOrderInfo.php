<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

readonly class SupplierOrderInfo
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('orderID')]
        public string $orderId,
        #[Serializer\XmlAttribute]
        public ?DateTimeInterface $orderDate = null,
    ) {
    }
}
