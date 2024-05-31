<?php

namespace CXml\Model\Response;

use CXml\Model\Option;
use CXml\Model\Transaction;
use JMS\Serializer\Annotation as Ser;

class ProfileResponse implements ResponsePayloadInterface
{
    #[Ser\XmlAttribute]
    private \DateTimeInterface $effectiveDate;

    #[Ser\XmlAttribute]
    private ?\DateTimeInterface $lastRefresh = null;

    /**
     *
     * @var Option[]
     */
    #[Ser\XmlList(inline: true, entry: 'Option')]
    #[Ser\Type('array<CXml\Model\Option>')]
    private array $options = [];

    /**
     *
     * @var Transaction[]
     */
    #[Ser\XmlList(inline: true, entry: 'Transaction')]
    #[Ser\Type('array<CXml\Model\Transaction>')]
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
