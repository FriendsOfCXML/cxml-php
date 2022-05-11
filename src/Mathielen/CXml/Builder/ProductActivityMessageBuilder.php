<?php

namespace Mathielen\CXml\Builder;

use Mathielen\CXml\Model\Contact;
use Mathielen\CXml\Model\Inventory;
use Mathielen\CXml\Model\InventoryQuantity;
use Mathielen\CXml\Model\ItemId;
use Mathielen\CXml\Model\Message\ProductActivityDetail;
use Mathielen\CXml\Model\Message\ProductActivityMessage;
use Mathielen\CXml\Model\MultilanguageString;

class ProductActivityMessageBuilder
{
	private ProductActivityMessage $productActivityMessage;
	private string $warehouseCodeDomain;

	private function __construct(string $messageId, string $warehouseCodeDomain)
	{
		$this->productActivityMessage = ProductActivityMessage::create(
			$messageId,
		);

		$this->warehouseCodeDomain = $warehouseCodeDomain;
	}

	public static function create(string $messageId, string $warehouseCodeDomain): self
	{
		return new self($messageId, $warehouseCodeDomain);
	}

	public function addProductActivityDetail(string $sku, string $warehouseCode, int $stockLevel, ?string $nextIntakeDate, ?int $nextIntakeQuantity): self
	{
		$inventory = Inventory::create()
			->setStockOnHandQuantity(new InventoryQuantity($stockLevel, 'EA'))
		;

		$activityDetail = ProductActivityDetail::create(
			new ItemId($sku, null, $sku),
			$inventory,
			Contact::create(new MultilanguageString($warehouseCode, null, 'en'), 'locationFrom')
				->addIdReference($this->warehouseCodeDomain, $warehouseCode)
		);

		// TODO found not better way to transport this info
		if ($nextIntakeDate) {
			$activityDetail->addExtrinsic('next_intake_date', $nextIntakeDate);
		}
		// TODO found not better way to transport this info
		if ($nextIntakeQuantity) {
			$activityDetail->addExtrinsic('next_intake_quantity', (string) $nextIntakeQuantity);
		}

		$this->productActivityMessage->addProductActivityDetail($activityDetail);

		return $this;
	}

	public function build(): ProductActivityMessage
	{
		if (empty($this->productActivityMessage->getProductActivityDetails())) {
			throw new \RuntimeException('Cannot build ProductActivityMessage without any ProductActivityDetail');
		}

		return $this->productActivityMessage;
	}
}
