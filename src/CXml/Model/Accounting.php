<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Accounting
{
    private function __construct(
        #[Serializer\XmlAttribute]
        private string $name,
        /**
         * @var AccountingSegment[]
         */
        #[Serializer\XmlList(entry: 'AccountingSegment', inline: true)]
        #[Serializer\Type('array<CXml\Model\AccountingSegment>')]
        private array $accountingSegments,
        #[Serializer\SerializedName('Charge')]
        private MoneyWrapper $charge,
    ) {
    }
}
