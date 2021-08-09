<?php

namespace Mathielen\CXml\Model\Message;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Inventory;
use Mathielen\CXml\Model\ItemId;
use Mathielen\CXml\Model\MultilanguageString;

class ProductActivityDetail
{
	/**
	 * @Ser\SerializedName("ItemID")
	 */
	private ItemId $itemId;

	/**
	 * @Ser\SerializedName("Description")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?MultilanguageString $description;

	/**
	 * @Ser\SerializedName("Inventory")
	 */
	private ?Inventory $inventory;

	public function __construct(ItemId $itemId, ?Inventory $inventory = null, ?MultilanguageString $description = null)
	{
		$this->description = $description;
		$this->itemId = $itemId;
		$this->inventory = $inventory;
	}

	public static function create(ItemId $itemId, ?Inventory $inventory = null, ?MultilanguageString $description = null): self
	{
		return new self($itemId, $inventory, $description);
	}
}
