<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['credential'])]
readonly class OrganizationId
{
    public function __construct(
        #[Serializer\SerializedName('Credential')]
        private Credential $credential
    ) {
    }

    public function getCredential(): Credential
    {
        return $this->credential;
    }
}
