<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Extrinsic
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public string $name,
        #[Serializer\XmlValue]
        public string $value,
    ) {
    }
}
