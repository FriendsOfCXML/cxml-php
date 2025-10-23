<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\ItemOut;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['orderRequestHeader', 'itemOut'])]
class OrderRequest implements RequestPayloadInterface
{
    /**
     * @var ItemOut[]
     */
    #[Serializer\XmlList(entry: 'ItemOut', inline: true)]
    #[Serializer\Type('array<CXml\Model\ItemOut>')]
    private array $itemOut = [];

    protected function __construct(#[Serializer\SerializedName('OrderRequestHeader')]
        public readonly OrderRequestHeader $orderRequestHeader)
    {
    }

    public static function create(OrderRequestHeader $orderRequestHeader): self
    {
        return new self(
            $orderRequestHeader,
        );
    }

    public function addItems(array $items): self
    {
        Assertion::allIsInstanceOf($items, ItemOut::class);

        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    public function addItem(ItemOut $item): self
    {
        $this->itemOut[] = $item;

        return $this;
    }

    public function getItems(): array
    {
        return $this->itemOut;
    }
}
