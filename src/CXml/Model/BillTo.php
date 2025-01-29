<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['address', 'idReferences'])]
class BillTo
{
    use IdReferencesTrait;

    public function __construct(
        #[Serializer\SerializedName('Address')]
        public readonly Address $address,
    ) {
    }
}
