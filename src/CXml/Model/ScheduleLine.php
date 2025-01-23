<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

readonly class ScheduleLine
{
    private function __construct(
        #[Serializer\SerializedName('UnitOfMeasure')]
        private UnitOfMeasure $unitOfMeasure,
        #[Serializer\XmlAttribute]
        private int $quantity,
        #[Serializer\XmlAttribute]
        private DateTimeInterface $requestedDeliveryDate,
        #[Serializer\XmlAttribute]
        private ?int $lineNumber = null,
    ) {
    }
}
