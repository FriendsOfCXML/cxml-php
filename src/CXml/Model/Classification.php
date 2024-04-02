<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Classification
{
    /**
     * @Ser\XmlAttribute
     */
    private string $domain;

    /**
     * @Ser\XmlValue(cdata=false)
     */
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
