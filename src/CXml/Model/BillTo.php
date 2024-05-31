<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class BillTo
{
    use IdReferencesTrait;

    #[Serializer\SerializedName('Address')]
    private Address $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
