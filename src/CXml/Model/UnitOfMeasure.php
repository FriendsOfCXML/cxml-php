<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class UnitOfMeasure
{
    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        private string $value
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
