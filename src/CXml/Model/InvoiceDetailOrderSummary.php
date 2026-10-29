<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['subtotalAmount', 'comments'])]
readonly class InvoiceDetailOrderSummary
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public int $invoiceLineNumber,
        #[Serializer\SerializedName('SubtotalAmount')]
        public MoneyWrapper $subtotalAmount,
        #[Serializer\SerializedName('Comments')]
        public ?Comment $comments = null,
    ) {
    }
}
