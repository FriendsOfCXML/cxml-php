<?php

namespace CXml\Model\Request;

use CXml\Model\AddressWrapper;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\SelectedItem;
use CXml\Model\Url;
use JMS\Serializer\Annotation as Ser;

class PunchOutSetupRequestPayload implements RequestPayloadInterface
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
}
