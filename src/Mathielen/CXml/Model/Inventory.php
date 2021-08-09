<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Inventory
{
	/**
	 * @Ser\SerializedName("StockOnHandQuantity")
	 */
	private StockQuantity $stockOnHandQuantity;

	public function __construct(StockQuantity $stockOnHandQuantity)
	{
		$this->stockOnHandQuantity = $stockOnHandQuantity;
	}
}
