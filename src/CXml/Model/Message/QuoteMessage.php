<?php

namespace CXml\Model\Message;

use CXml\Model\MoneyWrapper;
use CXml\Model\OrganizationId;
use JMS\Serializer\Annotation as Ser;

class QuoteMessage implements MessagePayloadInterface
{
    /**
     * @Ser\SerializedName("QuoteMessageHeader")
     */
    private QuoteMessageHeader $quoteMessageHeader;

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
}
