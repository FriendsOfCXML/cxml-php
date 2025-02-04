<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

class PaymentTerm
{
    use ExtrinsicsTrait;

    public function __construct(
        #[Serializer\XmlAttribute]
        public readonly int $payInNumberOfDays,
    ) {
    }
}
