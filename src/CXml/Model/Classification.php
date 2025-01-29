<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value'])]
readonly class Classification
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public string $domain,
        #[Serializer\XmlValue(cdata: false)]
        public string $value,
    ) {
    }
}
