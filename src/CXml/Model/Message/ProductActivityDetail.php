<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use CXml\Model\Contact;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\Inventory;
use CXml\Model\ItemId;
use CXml\Model\MultilanguageString;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['itemId', 'description', 'contact', 'inventory'])]
class ProductActivityDetail
{
    use ExtrinsicsTrait;

    private function __construct(
        #[Serializer\SerializedName('ItemID')]
        private readonly ItemId $itemId,
        #[Serializer\SerializedName('Inventory')]
        private readonly ?Inventory $inventory = null,
        #[Serializer\SerializedName('Contact')]
        private readonly ?Contact $contact = null,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly ?MultilanguageString $description = null,
    ) {
    }

    public static function create(ItemId $itemId, Inventory $inventory = null, Contact $contact = null, MultilanguageString $description = null): self
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
