<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Extrinsic
{
    /**
     * @Ser\XmlAttribute
     */
    private string $name;

    /**
     * @Ser\XmlValue(cdata=false)
     */
    private string $value;

    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
