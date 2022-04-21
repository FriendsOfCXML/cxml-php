<?php

namespace Mathielen\CXml\Builder;

use Mathielen\CXml\Model\Address;
use Mathielen\CXml\Model\AddressWrapper;
use Mathielen\CXml\Model\CarrierIdentifier;
use Mathielen\CXml\Model\Comment;
use Mathielen\CXml\Model\Contact;
use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\Inventory;
use Mathielen\CXml\Model\ItemDetail;
use Mathielen\CXml\Model\ItemId;
use Mathielen\CXml\Model\ItemOut;
use Mathielen\CXml\Model\Message\ProductActivityDetail;
use Mathielen\CXml\Model\Message\ProductActivityMessage;
use Mathielen\CXml\Model\Money;
use Mathielen\CXml\Model\MoneyWrapper;
use Mathielen\CXml\Model\MultilanguageString;
use Mathielen\CXml\Model\PostalAddress;
use Mathielen\CXml\Model\Request\OrderRequest;
use Mathielen\CXml\Model\Request\OrderRequestHeader;
use Mathielen\CXml\Model\Shipping;
use Mathielen\CXml\Model\ShipTo;
use Mathielen\CXml\Model\InventoryQuantity;
use Mathielen\CXml\Model\Tax;
use Mathielen\CXml\Model\TransportInformation;

class ProductActivityMessageBuilder
{

	private ProductActivityMessage $productActivityMessage;

	private function __construct(string $messageId)
	{
		$this->productActivityMessage = ProductActivityMessage::create(
			$messageId,
		);
	}

	public static function create(string $messageId): self
	{
		return new self($messageId);
	}

	public function addProductActivityDetail(string $sku, string $warehouseCode, int $stockLevel, ?string $nextIntakeDate, ?int $nextIntakeQuantity): self
	{
		$inventory = Inventory::create()
			->setStockOnHandQuantity(new InventoryQuantity($stockLevel, 'EA'));

		$activityDetail = ProductActivityDetail::create(
			new ItemId($sku),
			$inventory,
			Contact::create(new MultilanguageString($warehouseCode, null, 'en'), 'locationFrom')
				->addIdReference('NetworkId', '0003')
		);

		//TODO found not better way to transport this info
		if ($nextIntakeDate) {
			$activityDetail->addExtrinsic('next_intake_date', $nextIntakeDate);
		}
		//TODO found not better way to transport this info
		if ($nextIntakeQuantity) {
			$activityDetail->addExtrinsic('next_intake_quantity', $nextIntakeQuantity);
		}

		$this->productActivityMessage->addProductActivityDetail($activityDetail);

		return $this;
	}

	public function build(): ProductActivityMessage
	{
		return $this->productActivityMessage;
	}

}
