<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class InvoiceDetailOrderSummary
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public int $invoiceLineNumber,
        #[Serializer\SerializedName('SubtotalAmount')]
        public MoneyWrapper $subtotalAmount,
    ) {
    }
}
