<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class InvoiceDetailOrderInfo
{
    public function __construct(
        #[Serializer\SerializedName('OrderReference')]
        public OrderReference $orderReference,
    ) {
    }
}
