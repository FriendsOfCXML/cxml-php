<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class TaxDetail
{
    public function __construct(
        #[Serializer\XmlAttribute]
        private string $category,
        #[Serializer\SerializedName('TaxAmount')]
        private MoneyWrapper $taxAmount,
        #[Serializer\XmlAttribute]
        private ?int $percentageRate = null,
        #[Serializer\XmlAttribute]
        private ?string $taxRateType = null,
    ) {
    }
}
