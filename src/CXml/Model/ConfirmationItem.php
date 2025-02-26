<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['quantity', 'lineNumber', 'unitOfMeasure', 'confirmationStatus'])]
class ConfirmationItem
{
    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        public readonly int $quantity,
        #[Serializer\XmlAttribute]
        public readonly int $lineNumber,
        #[Serializer\SerializedName('UnitOfMeasure')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly string $unitOfMeasure,
        /**
         * @var ConfirmationStatus[]
         */
        #[Serializer\XmlList(entry: 'ConfirmationStatus', inline: true)]
        #[Serializer\Type('array<CXml\Model\ConfirmationStatus>')]
        public array $confirmationStatus = [],
    ) {
    }

    public static function create(int $quantity, int $lineNumber, string $unitOfMeasure): self
    {
        return new self($quantity, $lineNumber, $unitOfMeasure);
    }

    public function addConfirmationStatus(ConfirmationStatus $confirmationStatus): self
    {
        $this->confirmationStatus[] = $confirmationStatus;

        return $this;
    }
}
