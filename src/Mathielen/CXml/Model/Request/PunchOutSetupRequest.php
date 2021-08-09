<?php

namespace Mathielen\CXml\Model\Request;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\AddressWrapper;
use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\RequestInterface;
use Mathielen\CXml\Model\SelectedItem;
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
	private ?AddressWrapper $shipTo = null;

	/**
	 * @Ser\SerializedName("SelectedItem")
	 */
	private ?SelectedItem $selectedItem = null;

	public function __construct(string $buyerCookie, string $browserFormPost, string $supplierSetup, ?AddressWrapper $shipTo = null, ?SelectedItem $selectedItem = null, string $operation = 'create')
	{
		$this->operation = $operation;
		$this->buyerCookie = $buyerCookie;
		$this->browserFormPost = new Url($browserFormPost);
		$this->supplierSetup = new Url($supplierSetup);
		$this->shipTo = $shipTo;
		$this->selectedItem = $selectedItem;
	}

	public function addExtrinsic(Extrinsic $extrinsic): self
	{
		$this->extrinsics[] = $extrinsic;

		return $this;
	}
}
