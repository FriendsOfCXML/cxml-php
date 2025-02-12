<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use CXml\Model\ConfirmationItem;
use CXml\Model\OrderReference;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['confirmationHeader', 'orderReference', 'confirmationItems'])]
class ConfirmationRequest implements RequestPayloadInterface
{
    public function __construct(
        #[Serializer\SerializedName('ConfirmationHeader')]
        public readonly ConfirmationHeader $confirmationHeader,
        #[Serializer\SerializedName('OrderReference')]
        public readonly OrderReference $orderReference,
        /**
         * @var ConfirmationItem[]
         */
        #[Serializer\XmlList(entry: 'ConfirmationItem', inline: true)]
        #[Serializer\Type('array<CXml\Model\ConfirmationItem>')]
        public array $confirmationItems = [],
    ) {
    }

    public static function create(ConfirmationHeader $confirmationHeader, OrderReference $orderReference): self
    {
        return new self($confirmationHeader, $orderReference);
    }

    public function addConfirmationItem(ConfirmationItem $confirmationItem): self
    {
        $this->confirmationItems[] = $confirmationItem;

        return $this;
    }
}
