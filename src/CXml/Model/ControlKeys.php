<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class ControlKeys
{
    private function __construct(
        #[Serializer\SerializedName('InvoiceInstruction')]
        public InvoiceInstruction $invoiceInstruction,
    ) {
    }
}
