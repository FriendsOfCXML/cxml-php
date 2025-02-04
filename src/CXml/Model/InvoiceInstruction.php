<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class InvoiceInstruction
{
    private function __construct(
        #[Serializer\XmlAttribute]
        public string $verificationType,
        #[Serializer\XmlAttribute]
        public string $value,
    ) {
    }
}
