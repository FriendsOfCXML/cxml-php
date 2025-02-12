<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['documentReference'])]
readonly class OrderReference
{
    public function __construct(
        #[Serializer\SerializedName('DocumentReference')]
        public DocumentReference $documentReference,
        #[Serializer\SerializedName('orderID')]
        #[Serializer\XmlAttribute]
        public ?string $orderId = null,
        #[Serializer\XmlAttribute]
        public ?DateTimeInterface $orderDate = null,
    ) {
    }

    public static function create(string $documentReference): self
    {
        return new self(
            new DocumentReference($documentReference),
        );
    }
}
