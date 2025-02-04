<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use CXml\Model\Contact;
use CXml\Model\Inventory;
use CXml\Model\ItemId;
use CXml\Model\MultilanguageString;
use CXml\Model\Trait\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['itemId', 'description', 'contact', 'inventory'])]
class ProductActivityDetail
{
    use ExtrinsicsTrait;

    private function __construct(
        #[Serializer\SerializedName('ItemID')]
        public readonly ItemId $itemId,
        #[Serializer\SerializedName('Inventory')]
        public readonly ?Inventory $inventory = null,
        #[Serializer\SerializedName('Contact')]
        public readonly ?Contact $contact = null,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly ?MultilanguageString $description = null,
    ) {
    }

    public static function create(ItemId $itemId, ?Inventory $inventory = null, ?Contact $contact = null, ?MultilanguageString $description = null): self
    {
        return new self($itemId, $inventory, $contact, $description);
    }
}
