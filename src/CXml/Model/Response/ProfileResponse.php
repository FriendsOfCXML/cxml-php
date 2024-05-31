<?php

namespace CXml\Model\Response;

use CXml\Model\Option;
use CXml\Model\Transaction;
use JMS\Serializer\Annotation as Serializer;

class ProfileResponse implements ResponsePayloadInterface
{
    #[Serializer\XmlAttribute]
    private \DateTimeInterface $effectiveDate;

    #[Serializer\XmlAttribute]
    private ?\DateTimeInterface $lastRefresh = null;

    /**
     * @var Option[]
     */
    #[Serializer\XmlList(inline: true, entry: 'Option')]
    #[Serializer\Type('array<CXml\Model\Option>')]
    private array $options = [];

    /**
     * @var Transaction[]
     */
    #[Serializer\XmlList(inline: true, entry: 'Transaction')]
    #[Serializer\Type('array<CXml\Model\Transaction>')]
    private array $transactions = [];

    public function __construct(\DateTimeInterface $effectiveDate = null, \DateTimeInterface $lastRefresh = null)
    {
        $this->effectiveDate = $effectiveDate ?? new \DateTime();
        $this->lastRefresh = $lastRefresh;
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
