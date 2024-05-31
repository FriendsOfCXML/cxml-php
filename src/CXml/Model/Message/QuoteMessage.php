<?php

namespace CXml\Model\Message;

use CXml\Model\ItemIn;
use CXml\Model\MoneyWrapper;
use CXml\Model\OrganizationId;
use JMS\Serializer\Annotation as Ser;

class QuoteMessage implements MessagePayloadInterface
{
    #[Ser\SerializedName('QuoteMessageHeader')]
    private QuoteMessageHeader $quoteMessageHeader;

    /**
     *
     * @var ItemIn[]
     */
    #[Ser\XmlList(inline: true, entry: 'QuoteItemIn')]
    #[Ser\Type('array<CXml\Model\ItemIn>')]
    private array $quoteMessageItems = [];

    private function __construct(QuoteMessageHeader $quoteMessageHeader)
    {
        $this->quoteMessageHeader = $quoteMessageHeader;
    }

    public static function create(OrganizationId $organizationId, MoneyWrapper $total, string $type, string $quoteId, \DateTime $quoteDate, string $lang = 'en'): self
    {
        return new self(
            new QuoteMessageHeader($organizationId, $total, $type, $quoteId, $quoteDate, $total->getMoney()->getCurrency(), $lang)
        );
    }

    public function getQuoteMessageHeader(): QuoteMessageHeader
    {
        return $this->quoteMessageHeader;
    }

    public function getItems(): array
    {
        return $this->quoteMessageItems;
    }

    public function addItem(ItemIn $quoteMessageItem): self
    {
        $this->quoteMessageItems[] = $quoteMessageItem;

        return $this;
    }
}
