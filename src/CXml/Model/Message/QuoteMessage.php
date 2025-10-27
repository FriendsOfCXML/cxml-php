<?php

declare(strict_types=1);

namespace CXml\Model\Message;

use CXml\Model\MoneyWrapper;
use CXml\Model\OrganizationId;
use CXml\Model\QuoteItemIn;
use DateTime;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['quoteMessageHeader', 'quoteMessageItems'])]
class QuoteMessage implements MessagePayloadInterface
{
    /**
     * @var QuoteItemIn[]
     */
    #[Serializer\XmlList(entry: 'QuoteItemIn', inline: true)]
    #[Serializer\Type('array<CXml\Model\QuoteItemIn>')]
    private array $quoteMessageItems = [];

    public function __construct(#[Serializer\SerializedName('QuoteMessageHeader')]
        public readonly QuoteMessageHeader $quoteMessageHeader)
    {
    }

    public static function create(OrganizationId $organizationId, MoneyWrapper $total, string $type, string $quoteId, DateTime $quoteDate, string $lang = 'en'): self
    {
        return new self(
            new QuoteMessageHeader($organizationId, $total, $type, $quoteId, $quoteDate, $total->money->currency, $lang),
        );
    }

    public function getItems(): array
    {
        return $this->quoteMessageItems;
    }

    public function addItem(QuoteItemIn $quoteMessageItem): self
    {
        $this->quoteMessageItems[] = $quoteMessageItem;

        return $this;
    }
}
