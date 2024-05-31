<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Extrinsic
{
    public function __construct(
        #[Serializer\XmlAttribute]
        private string $name,
        #[Serializer\XmlValue(cdata: false)]
        private string $value,
    ) {
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
