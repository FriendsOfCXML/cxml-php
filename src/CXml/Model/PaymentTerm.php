<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class PaymentTerm
{
    use ExtrinsicsTrait;

    public function __construct(
        #[Serializer\XmlAttribute]
        private int $payInNumberOfDays,
    ) {
    }
}
