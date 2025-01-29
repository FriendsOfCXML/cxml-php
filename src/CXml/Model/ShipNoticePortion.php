<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['orderReference'])]
readonly class ShipNoticePortion
{
    #[Serializer\SerializedName('OrderReference')]
    public OrderReference $orderReference;

    public function __construct(string $documentReference, ?string $orderId = null, ?DateTimeInterface $orderDate = null)
    {
        $this->orderReference = new OrderReference(
            new DocumentReference(
                $documentReference,
            ),
            $orderId,
            $orderDate,
        );
    }
}
