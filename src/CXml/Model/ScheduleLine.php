<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

readonly class ScheduleLine
{
    private function __construct(
        #[Serializer\SerializedName('UnitOfMeasure')]
        #[Serializer\XmlElement(cdata: false)]
        public string $unitOfMeasure,
        #[Serializer\XmlAttribute]
        public int $quantity,
        #[Serializer\XmlAttribute]
        public DateTimeInterface $requestedDeliveryDate,
        #[Serializer\XmlAttribute]
        public ?int $lineNumber = null,
    ) {
    }
}
