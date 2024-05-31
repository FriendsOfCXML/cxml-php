<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['address', 'idReferences'])]
class BillTo
{
    use IdReferencesTrait;

    public function __construct(
        #[Serializer\SerializedName('Address')]
        private readonly Address $address
    ) {
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
