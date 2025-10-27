<?php

declare(strict_types=1);

namespace CXml\Model;

use Assert\Assertion;
use CXml\Model\Message\QuoteMessageHeader;
use CXml\Model\Trait\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['type', 'quantity', 'itemId', 'itemDetail'])]
class QuoteItemIn
{
    use ExtrinsicsTrait;
    protected function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('type')]
        readonly public string $type,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('quantity')]
        readonly public int $quantity,
        #[Serializer\SerializedName('ItemID')]
        readonly public ItemId $itemId,
        #[Serializer\SerializedName('ItemDetail')]
        readonly public ItemDetail $itemDetail,
    ) {
        Assertion::inArray($type, [
            QuoteMessageHeader::TYPE_ACCEPT,
            QuoteMessageHeader::TYPE_REJECT,
            QuoteMessageHeader::TYPE_UPDATE,
            QuoteMessageHeader::TYPE_FINAL,
            QuoteMessageHeader::TYPE_AWARD,
        ]);
    }

    public static function create(
        string $type,
        int $quantity,
        ItemId $itemId,
        ItemDetail $itemDetail,
    ): self {
        return new self(
            $type,
            $quantity,
            $itemId,
            $itemDetail,
        );
    }

    public function addClassification(string $domain, string $value): self
    {
        $classification = new Classification($domain, $value);
        $this->itemDetail->addClassification($classification);

        return $this;
    }
}
