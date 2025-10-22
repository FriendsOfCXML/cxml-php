<?php

declare(strict_types=1);

namespace CXml\Builder;

use Assert\Assertion;
use CXml\Model\Contact;
use CXml\Model\Inventory;
use CXml\Model\InventoryQuantity;
use CXml\Model\ItemId;
use CXml\Model\Message\ProductActivityDetail;
use CXml\Model\Message\ProductActivityMessage;
use CXml\Model\MultilanguageString;
use RuntimeException;

readonly class ProductActivityMessageBuilder
{
    private ProductActivityMessage $productActivityMessage;

    private function __construct(string $messageId, private string $warehouseCodeDomain)
    {
        $this->productActivityMessage = ProductActivityMessage::create(
            $messageId,
        );
    }

    public static function create(string $messageId, string $warehouseCodeDomain): self
    {
        return new self($messageId, $warehouseCodeDomain);
    }

    public function addProductActivityDetail(string $sku, string $warehouseCode, int $stockLevel, ?array $extrinsics = null): self
    {
        $inventory = Inventory::create()
            ->setStockOnHandQuantity(new InventoryQuantity($stockLevel, 'EA'));

        $activityDetail = ProductActivityDetail::create(
            new ItemId($sku, null, $sku),
            $inventory,
            Contact::create(new MultilanguageString($warehouseCode, null, 'en'), 'locationFrom')
                ->addIdReference($this->warehouseCodeDomain, $warehouseCode),
        );

        if (null !== $extrinsics && [] !== $extrinsics) {
            foreach ($extrinsics as $k => $v) {
                Assertion::string($k, 'Extrinsics key must be a string');
                Assertion::string($v, 'Extrinsics value must be a string');
                $activityDetail->addExtrinsicAsKeyValue($k, $v);
            }
        }

        $this->productActivityMessage->addProductActivityDetail($activityDetail);

        return $this;
    }

    public function build(): ProductActivityMessage
    {
        if ([] === $this->productActivityMessage->getProductActivityDetails()) {
            throw new RuntimeException('Cannot build ProductActivityMessage without any ProductActivityDetail');
        }

        return $this->productActivityMessage;
    }
}
