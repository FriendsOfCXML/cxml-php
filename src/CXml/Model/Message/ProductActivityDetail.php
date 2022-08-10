<?php

namespace CXml\Model\Message;

use CXml\Model\Contact;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\Inventory;
use CXml\Model\ItemId;
use CXml\Model\MultilanguageString;
use JMS\Serializer\Annotation as Ser;

class ProductActivityDetail
{
	use ExtrinsicsTrait;

	/**
	 * @Ser\SerializedName("ItemID")
	 */
	private ItemId $itemId;

	/**
	 * @Ser\SerializedName("Description")
	 * @Ser\XmlElement (cdata=false)
	 */
	private ?MultilanguageString $description = null;

	/**
	 * @Ser\SerializedName("Contact")
	 * todo: more contact should be allowed
	 */
	private ?Contact $contact = null;

	/**
	 * @Ser\SerializedName("Inventory")
	 */
	private ?Inventory $inventory = null;

	private function __construct(ItemId $itemId, ?Inventory $inventory = null, ?Contact $contact = null, ?MultilanguageString $description = null)
	{
		$this->contact = $contact;
		$this->description = $description;
		$this->itemId = $itemId;
		$this->inventory = $inventory;
	}

	public static function create(ItemId $itemId, ?Inventory $inventory = null, ?Contact $contact = null, ?MultilanguageString $description = null): self
	{
		return new self($itemId, $inventory, $contact, $description);
	}

	public function getItemId(): ItemId
	{
		return $this->itemId;
	}

	public function getDescription(): ?MultilanguageString
	{
		return $this->description;
	}

	public function getInventory(): ?Inventory
	{
		return $this->inventory;
	}

	public function getContact(): ?Contact
	{
		return $this->contact;
	}
}
