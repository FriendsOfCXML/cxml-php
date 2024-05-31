<?php

namespace CXml\Model\Request;

use CXml\Model\Extrinsic;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\ItemOut;
use CXml\Model\SelectedItem;
use CXml\Model\ShipTo;
use CXml\Model\Url;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['buyerCookie', 'extrinsics', 'browserFormPost', 'supplierSetup', 'shipTo', 'selectedItem', 'itemOut'])]
class PunchOutSetupRequest implements RequestPayloadInterface
{
    use ExtrinsicsTrait;

    /**
     * @var Extrinsic[]
     */
    #[Serializer\XmlList(entry: 'Extrinsic', inline: true)]
    #[Serializer\Type('array<CXml\Model\Extrinsic>')]
    protected array $extrinsics = [];

    #[Serializer\SerializedName('BrowserFormPost')]
    private Url $browserFormPost;

    #[Serializer\SerializedName('SupplierSetup')]
    private Url $supplierSetup;

    /**
     * @var ItemOut[]
     */
    #[Serializer\XmlList(entry: 'ItemOut', inline: true)]
    #[Serializer\Type('array<CXml\Model\ItemOut>')]
    private array $itemOut = [];

    public function __construct(
        #[Serializer\SerializedName('BuyerCookie')]
        private readonly string $buyerCookie,
        string $browserFormPost,
        string $supplierSetup,
        #[Serializer\SerializedName('ShipTo')]
        private readonly ?ShipTo $shipTo = null,
        #[Serializer\SerializedName('SelectedItem')]
        private readonly ?SelectedItem $selectedItem = null,
        #[Serializer\XmlAttribute]
        private readonly ?string $operation = 'create'
    ) {
        $this->browserFormPost = new Url($browserFormPost);
        $this->supplierSetup = new Url($supplierSetup);
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function getBuyerCookie(): string
    {
        return $this->buyerCookie;
    }

    public function getBrowserFormPost(): Url
    {
        return $this->browserFormPost;
    }

    public function getSupplierSetup(): Url
    {
        return $this->supplierSetup;
    }

    public function getShipTo(): ?ShipTo
    {
        return $this->shipTo;
    }

    public function getSelectedItem(): ?SelectedItem
    {
        return $this->selectedItem;
    }

    public function getItems(): array
    {
        return $this->itemOut;
    }

    public function addItems(array $items): self
    {
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
