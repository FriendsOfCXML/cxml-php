<?php

declare(strict_types=1);

namespace CXml\Model\Extension;

use CXml\Model\PaymentInterface;
use CXml\Model\Trait\ExtrinsicsTrait;
use CXml\Model\Trait\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

class PaymentService implements PaymentInterface
{
    use IdReferencesTrait;
    use ExtrinsicsTrait;

    public function __construct(
        #[Serializer\XmlAttribute]
        public readonly string $method,
        #[Serializer\XmlAttribute]
        public readonly ?string $provider = null,
    ) {
    }
}
