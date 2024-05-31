<?php

namespace CXml\Model\Message;

use CXml\Model\ItemIn;
use JMS\Serializer\Annotation as Serializer;

class PunchOutOrderMessage implements MessagePayloadInterface
{
    #[Serializer\SerializedName('BuyerCookie')]
    #[Serializer\XmlElement(cdata: false)]
    private string $buyerCookie;

    #[Serializer\SerializedName('PunchOutOrderMessageHeader')]
    private PunchOutOrderMessageHeader $punchOutOrderMessageHeader;

    /**
     * @var ItemIn[]
     */
    #[Serializer\XmlList(inline: true, entry: 'ItemIn')]
    #[Serializer\Type('array<CXml\Model\ItemIn>')]
    private array $punchoutOrderMessageItems = [];

    private function __construct(string $buyerCookie, PunchOutOrderMessageHeader $punchOutOrderMessageHeader)
    {
        $this->buyerCookie = $buyerCookie;
        $this->punchOutOrderMessageHeader = $punchOutOrderMessageHeader;
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
