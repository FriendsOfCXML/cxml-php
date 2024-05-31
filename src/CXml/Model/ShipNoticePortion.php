<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class ShipNoticePortion
{
    #[Serializer\SerializedName('OrderReference')]
    private OrderReference $orderReference;

    public function __construct(string $documentReference, string $orderId = null, \DateTimeInterface $orderDate = null)
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
