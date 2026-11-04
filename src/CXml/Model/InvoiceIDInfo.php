<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

readonly class InvoiceIDInfo
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('invoiceID')]
        public string $invoiceId,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('invoiceDate')]
        public ?DateTimeInterface $invoiceDate = null,
    ) {
    }
}
