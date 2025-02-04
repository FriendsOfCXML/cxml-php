<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class TaxDetail
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public string $category,
        #[Serializer\SerializedName('TaxAmount')]
        public MoneyWrapper $taxAmount,
        #[Serializer\XmlAttribute]
        public ?int $percentageRate = null,
        #[Serializer\XmlAttribute]
        public ?string $taxRateType = null,
    ) {
    }
}
