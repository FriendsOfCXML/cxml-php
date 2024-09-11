<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use CXml\Model\ItemIn;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['buyerCookie', 'punchOutOrderMessageHeader', 'punchoutOrderMessageItems'])]
class PunchOutOrderMessage implements MessagePayloadInterface
{
    /**
     * @var ItemIn[]
     */
    #[Serializer\XmlList(entry: 'ItemIn', inline: true)]
    #[Serializer\Type('array<CXml\Model\ItemIn>')]
    private array $punchoutOrderMessageItems = [];

    private function __construct(#[Serializer\SerializedName('BuyerCookie')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly string $buyerCookie, #[Serializer\SerializedName('PunchOutOrderMessageHeader')]
        private readonly PunchOutOrderMessageHeader $punchOutOrderMessageHeader)
    {
    }

    public static function create(string $buyerCookie, PunchOutOrderMessageHeader $punchOutOrderMessageHeader): self
    {
        return new self($buyerCookie, $punchOutOrderMessageHeader);
    }

    public function getBuyerCookie(): string
    {
        return $this->buyerCookie;
    }

    public function getPunchOutOrderMessageHeader(): PunchOutOrderMessageHeader
    {
        return $this->punchOutOrderMessageHeader;
    }

    public function getPunchoutOrderMessageItems(): array
    {
        return $this->punchoutOrderMessageItems;
    }

    public function addPunchoutOrderMessageItem(ItemIn $punchoutOrderMessageItem): self
    {
        $this->punchoutOrderMessageItems[] = $punchoutOrderMessageItem;

        return $this;
    }
}
