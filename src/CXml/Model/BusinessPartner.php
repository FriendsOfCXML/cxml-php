<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\IdReferencesTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['type', 'role', 'address', 'idReferences'])]
readonly class BusinessPartner
{
    use IdReferencesTrait;
    final public const ROLE_SOLD_TO = 'soldTo';

    final public const ROLE_SHIP_FROM = 'shipFrom';

    final public const ROLE_ORDERING_ADDRESS = 'orderingAddress';

    public function __construct(
        #[Serializer\XmlAttribute]
        public string $role,
        #[Serializer\SerializedName('Address')]
        public Address $address,
        #[Serializer\XmlAttribute]
        public string $type = 'organization',
    ) {
    }
}
