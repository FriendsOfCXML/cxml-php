<?php

namespace Mathielen\CXml\Model\Message;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Contact;
use Mathielen\CXml\Model\ExtrinsicsTrait;
use Mathielen\CXml\Model\Inventory;
use Mathielen\CXml\Model\ItemId;
use Mathielen\CXml\Model\MultilanguageString;

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
	private ?MultilanguageString $description;

	/**
	 * @Ser\SerializedName("Contact")
	 * todo: more contact should be allowed
	 */
	private ?Contact $contact;

	/**
	 * @Ser\SerializedName("Inventory")
	 */
	private ?Inventory $inventory;

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
