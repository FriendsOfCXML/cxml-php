<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use CXml\Model\OrderReference;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['confirmationHeader', 'orderReference'])]
readonly class ConfirmationRequest implements RequestPayloadInterface
{
    public function __construct(
        #[Serializer\SerializedName('ConfirmationHeader')]
        public ConfirmationHeader $confirmationHeader,
        #[Serializer\SerializedName('OrderReference')]
        public OrderReference $orderReference,
    ) {
    }

    public static function create(ConfirmationHeader $confirmationHeader, OrderReference $orderReference): self
    {
        return new self($confirmationHeader, $orderReference);
    }
}
