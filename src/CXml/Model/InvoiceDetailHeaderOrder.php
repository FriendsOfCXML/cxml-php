<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['invoiceDetailOrderInfo', 'invoiceDetailOrderSummary'])]
readonly class InvoiceDetailHeaderOrder
{
    public function __construct(
        #[Serializer\SerializedName('InvoiceDetailOrderInfo')]
        public InvoiceDetailOrderInfo $invoiceDetailOrderInfo,
        #[Serializer\SerializedName('InvoiceDetailOrderSummary')]
        public InvoiceDetailOrderSummary $invoiceDetailOrderSummary,
    ) {
    }
}
