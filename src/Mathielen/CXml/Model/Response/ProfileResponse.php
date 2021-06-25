<?php

namespace Mathielen\CXml\Model\Response;

use Mathielen\CXml\Model\Option;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Model\Transaction;
use JMS\Serializer\Annotation as Ser;

class ProfileResponse implements ResponseInterface
{
    /**
     * @Ser\XmlAttribute
     */
    private \DateTime $effectiveDate;

    /**
     * @Ser\XmlAttribute
     */
    private ?\DateTime $lastRefresh = null;

    /**
     * @Ser\XmlList(inline=true, entry="Option")
     *
     * @var Option[]
     */
    private array $options = [];

    /**
     * @Ser\XmlList(inline=true, entry="Transaction")
     *
     * @var Transaction[]
     */
    private array $transactions = [];

    public function __construct(\DateTime $effectiveDate = null, ?\DateTime $lastRefresh = null)
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
