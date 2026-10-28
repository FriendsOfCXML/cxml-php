<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['subtotalAmount', 'tax', 'netAmount'])]
readonly class InvoiceDetailSummary
{
    public function __construct(
        #[Serializer\SerializedName('SubtotalAmount')]
        public MoneyWrapper $subtotalAmount,
        #[Serializer\SerializedName('Tax')]
        public Tax $tax,
        #[Serializer\SerializedName('NetAmount')]
        public MoneyWrapper $netAmount,
    ) {
    }
}
