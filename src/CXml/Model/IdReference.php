<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class IdReference
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public string $domain,
        #[Serializer\XmlAttribute]
        public string $identifier,
    ) {
    }
}
