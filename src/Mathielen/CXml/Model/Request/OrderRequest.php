<?php

namespace Mathielen\CXml\Model\Request;

use Mathielen\CXml\Model\ItemOut;
use Mathielen\CXml\Model\RequestInterface;
use JMS\Serializer\Annotation as Ser;

class OrderRequest implements RequestInterface
{
    /**
     * @Ser\SerializedName("OrderRequestHeader")
     */
    private OrderRequestHeader $orderRequestHeader;

    /**
     * @Ser\XmlList(inline=true, entry="ItemOut")
     * @Ser\Type("array<Mathielen\CXml\Model\ItemOut>")
     *
     * @var ItemOut[]
     */
    private array $itemOut = [];

    public function __construct(OrderRequestHeader $orderRequestHeader)
    {
        $this->orderRequestHeader = $orderRequestHeader;
    }

    public function addItem(ItemOut $item): self
    {
        $this->itemOut[] = $item;

        return $this;
    }
}
