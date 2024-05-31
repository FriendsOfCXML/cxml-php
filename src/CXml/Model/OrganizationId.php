<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class OrganizationId
{
    #[Serializer\SerializedName('Credential')]
    private Credential $credential;

    public function __construct(Credential $credential)
    {
        $this->credential = $credential;
    }

    public function getCredential(): Credential
    {
        return $this->credential;
    }
}
