<?php

namespace Mathielen\CXml\Model\Request;

use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\RequestInterface;
use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\SelectedItem;
use Mathielen\CXml\Model\ShipTo;
use Mathielen\CXml\Model\Url;

class PunchOutSetupRequest implements RequestInterface
{
    /**
     * @Ser\XmlAttribute
     */
    private ?string $operation = null;

    /**
     * @Ser\SerializedName("BuyerCookie")
     */
    private string $buyerCookie;

    /**
     * @Ser\XmlList(inline=true, entry="Extrinsic")
     * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
     *
     * @var Extrinsic[]
     */
    private array $extrinsics = [];

    /**
     * @Ser\SerializedName("BrowserFormPost")
     */
    private Url $browserFormPost;

    /**
     * @Ser\SerializedName("SupplierSetup")
     */
    private Url $supplierSetup;

    /**
     * @Ser\SerializedName("ShipTo")
     */
    private ?ShipTo $shipTo = null;

    /**
     * @Ser\SerializedName("SelectedItem")
     */
    private ?SelectedItem $selectedItem = null;

    public function __construct(string $buyerCookie, string $browserFormPost, string $supplierSetup, ?ShipTo $shipTo=null, ?SelectedItem $selectedItem=null, string $operation = 'create')
    {
        $this->operation = $operation;
        $this->buyerCookie = $buyerCookie;
        $this->browserFormPost = new Url($browserFormPost);
        $this->supplierSetup = new Url($supplierSetup);
        $this->shipTo = $shipTo;
        $this->selectedItem = $selectedItem;
    }
}
