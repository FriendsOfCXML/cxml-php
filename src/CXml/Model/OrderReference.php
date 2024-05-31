<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['documentReference'])]
readonly class OrderReference
{
    public function __construct(
        #[Serializer\SerializedName('DocumentReference')]
        private ?DocumentReference $documentReference,
        #[Serializer\SerializedName('orderID')]
        #[Serializer\XmlAttribute]
        private ?string $orderId = null,
        #[Serializer\XmlAttribute]
        private ?\DateTimeInterface $orderDate = null,
    ) {
    }

    public static function create(string $documentReference): self
    {
        return new self(
            new DocumentReference($documentReference),
        );
    }

    public function getDocumentReference(): ?DocumentReference
    {
        return $this->documentReference;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }
}
