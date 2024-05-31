<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class IdReference
{
    public function __construct(
        #[Serializer\XmlAttribute]
        private string $domain,
        #[Serializer\XmlAttribute]
        private string $identifier)
    {
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
