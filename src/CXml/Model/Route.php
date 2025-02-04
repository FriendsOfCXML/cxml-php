<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Route
{
    final public const METHOD_UNKNOWN = 'unknown';

    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        public string $value,
        #[Serializer\XmlAttribute]
        public string $method = self::METHOD_UNKNOWN,
    ) {
    }
}
