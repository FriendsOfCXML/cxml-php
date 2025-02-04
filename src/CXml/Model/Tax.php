<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['money', 'description'])]
class Tax
{
    #[Serializer\SerializedName('Money')]
    public readonly Money $money;

    /**
     * @var TaxDetail[]
     */
    #[Serializer\XmlList(entry: 'TaxDetail', inline: true)]
    #[Serializer\Type('array<CXml\Model\TaxDetail>')]
    private array $taxDetails = [];

    public function __construct(
        string $currency,
        int $value,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly MultilanguageString $description,
    ) {
        $this->money = new Money($currency, $value);
    }

    public function addTaxDetail(TaxDetail $taxDetail): void
    {
        $this->taxDetails[] = $taxDetail;
    }
}
