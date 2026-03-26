<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['leadtime', 'expirationDate', 'territoryAvailables'])]
class IndexItemDetail
{
    /**
     * @var TerritoryAvailable[]
     */
    #[Serializer\XmlList(entry: 'TerritoryAvailable', inline: true)]
    #[Serializer\Type('array<CXml\Model\TerritoryAvailable>')]
    private array $territoryAvailables = [];

    /**
     * @param string[] $territoryAvailables
     */
    public function __construct(
        #[Serializer\SerializedName('LeadTime')]
        #[Serializer\XmlElement(cdata: false)]
        public int $leadtime,
        #[Serializer\SerializedName('ExpirationDate')]
        #[Serializer\XmlElement(cdata: false)]
        public ?DateTimeInterface $expirationDate = null,
        array $territoryAvailables = [],
    ) {
        $this->territoryAvailables = array_map(fn (string $territory): TerritoryAvailable => new TerritoryAvailable($territory), $territoryAvailables);
    }
}
