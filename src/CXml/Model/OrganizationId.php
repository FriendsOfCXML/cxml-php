<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['credential'])]
readonly class OrganizationId
{
    public function __construct(
        #[Serializer\SerializedName('Credential')]
        public Credential $credential,
    ) {
    }
}
