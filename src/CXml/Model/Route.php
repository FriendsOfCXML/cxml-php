<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Route
{
    final public const METHOD_UNKNOWN = 'unknown';

    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        private string $value,
        #[Serializer\XmlAttribute]
        private string $method = self::METHOD_UNKNOWN,
    ) {
    }
}
