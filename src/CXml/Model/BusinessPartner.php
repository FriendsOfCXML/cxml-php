<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['type', 'role', 'address', 'idReferences'])]
class BusinessPartner
{
    use IdReferencesTrait;
    final public const ROLE_SOLD_TO = 'soldTo';

    final public const ROLE_SHIP_FROM = 'shipFrom';

    final public const ROLE_ORDERING_ADDRESS = 'orderingAddress';

    public function __construct(
        #[Serializer\XmlAttribute]
        private string $role,
        #[Serializer\SerializedName('Address')]
        private Address $address,
        #[Serializer\XmlAttribute]
        private string $type = 'organization',
    ) {
    }
}
