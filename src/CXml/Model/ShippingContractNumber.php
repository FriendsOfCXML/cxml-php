<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class ShippingContractNumber
{
    #[Serializer\XmlValue(cdata: false)]
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
