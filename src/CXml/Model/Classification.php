<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Classification
{
    #[Serializer\XmlAttribute]
    private string $domain;

    #[Serializer\XmlValue(cdata: false)]
    private string $value;

    public function __construct(string $domain, string $value)
    {
        $this->domain = $domain;
        $this->value = $value;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
