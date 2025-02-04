<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class MultilanguageString
{
    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        public ?string $value = null,
        #[Serializer\XmlAttribute]
        public ?string $type = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        public ?string $lang = 'en',
    ) {
    }
}
