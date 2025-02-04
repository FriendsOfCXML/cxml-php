<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Accounting
{
    private function __construct(
        #[Serializer\XmlAttribute]
        public string $name,
        /**
         * @var AccountingSegment[]
         */
        #[Serializer\XmlList(entry: 'AccountingSegment', inline: true)]
        #[Serializer\Type('array<CXml\Model\AccountingSegment>')]
        public array $accountingSegments,
        #[Serializer\SerializedName('Charge')]
        public MoneyWrapper $charge,
    ) {
    }
}
