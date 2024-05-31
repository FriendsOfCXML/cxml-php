<?php

namespace CXml\Model\Response;

use CXml\Model\Option;
use CXml\Model\Transaction;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['options', 'transactions'])]
class ProfileResponse implements ResponsePayloadInterface
{
    #[Serializer\XmlAttribute]
    private readonly \DateTimeInterface $effectiveDate;
    /**
     * @var Option[]
     */
    #[Serializer\XmlList(entry: 'Option', inline: true)]
    #[Serializer\Type('array<CXml\Model\Option>')]
    private array $options = [];

    /**
     * @var Transaction[]
     */
    #[Serializer\XmlList(inline: true, entry: 'Transaction')]
    #[Serializer\Type('array<CXml\Model\Transaction>')]
    private array $transactions = [];

    public function __construct(
        \DateTimeInterface $effectiveDate = null,
        #[Serializer\XmlAttribute]
        private readonly ?\DateTimeInterface $lastRefresh = null
    ) {
        $this->effectiveDate = $effectiveDate ?? new \DateTime();
    }

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }
}
