<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Inventory
{
    #[Serializer\SerializedName('StockOnHandQuantity')]
    private ?InventoryQuantity $stockOnHandQuantity = null;

    #[Serializer\SerializedName('IncrementQuantity')]
    private ?InventoryQuantity $incrementQuantity = null;

    public static function create(): self
    {
        return new self();
    }

    public function getStockOnHandQuantity(): ?InventoryQuantity
    {
        return $this->stockOnHandQuantity;
    }

    public function setStockOnHandQuantity(InventoryQuantity $stockOnHandQuantity): self
    {
        $this->stockOnHandQuantity = $stockOnHandQuantity;

        return $this;
    }

    public function getIncrementQuantity(): ?InventoryQuantity
    {
        return $this->incrementQuantity;
    }

    public function setIncrementQuantity(InventoryQuantity $incrementQuantity): self
    {
        $this->incrementQuantity = $incrementQuantity;

        return $this;
    }
}
