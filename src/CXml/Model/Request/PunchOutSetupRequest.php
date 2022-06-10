<?php

namespace CXml\Model\Request;

use CXml\Model\ExtrinsicsTrait;
use CXml\Model\SelectedItem;
use CXml\Model\ShipTo;
use CXml\Model\Url;
use JMS\Serializer\Annotation as Ser;

class PunchOutSetupRequest implements RequestPayloadInterface
{
	use ExtrinsicsTrait;

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $operation = null;

	/**
	 * @Ser\SerializedName("BuyerCookie")
	 */
	private string $buyerCookie;

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

	public function __construct(string $buyerCookie, string $browserFormPost, string $supplierSetup, ?ShipTo $shipTo = null, ?SelectedItem $selectedItem = null, string $operation = 'create')
	{
		$this->operation = $operation;
		$this->buyerCookie = $buyerCookie;
		$this->browserFormPost = new Url($browserFormPost);
		$this->supplierSetup = new Url($supplierSetup);
		$this->shipTo = $shipTo;
		$this->selectedItem = $selectedItem;
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
}
