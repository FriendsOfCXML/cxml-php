<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\ItemOut;
use CXml\Model\SelectedItem;
use CXml\Model\ShipTo;
use CXml\Model\Trait\ExtrinsicsTrait;
use CXml\Model\Url;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['buyerCookie', 'extrinsics', 'browserFormPost', 'supplierSetup', 'shipTo', 'selectedItem', 'itemOut'])]
class PunchOutSetupRequest implements RequestPayloadInterface
{
    use ExtrinsicsTrait;

    #[Serializer\SerializedName('BrowserFormPost')]
    public readonly Url $browserFormPost;

    #[Serializer\SerializedName('SupplierSetup')]
    public readonly Url $supplierSetup;

    /**
     * @var ItemOut[]
     */
    #[Serializer\XmlList(entry: 'ItemOut', inline: true)]
    #[Serializer\Type('array<CXml\Model\ItemOut>')]
    private array $itemOut = [];

    public function __construct(
        #[Serializer\SerializedName('BuyerCookie')]
        public readonly string $buyerCookie,
        string $browserFormPost,
        string $supplierSetup,
        #[Serializer\SerializedName('ShipTo')]
        public readonly ?ShipTo $shipTo = null,
        #[Serializer\SerializedName('SelectedItem')]
        public readonly ?SelectedItem $selectedItem = null,
        #[Serializer\XmlAttribute]
        public readonly ?string $operation = 'create',
    ) {
        $this->browserFormPost = new Url($browserFormPost);
        $this->supplierSetup = new Url($supplierSetup);
    }

    public function getItems(): array
    {
        return $this->itemOut;
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
}
