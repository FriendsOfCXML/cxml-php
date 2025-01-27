<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Classification
{
    public function __construct(
        #[Serializer\XmlAttribute]
        private string $domain,
        #[Serializer\XmlValue(cdata: false)]
        private string $value,
    ) {
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
