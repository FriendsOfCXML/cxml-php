<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class ShippingContractNumber
{
    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        private string $value
    ) {
    }
}
