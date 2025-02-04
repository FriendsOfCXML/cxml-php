<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Status
{
    public function __construct(
        #[Serializer\XmlAttribute]
        public int $code = 200,
        #[Serializer\XmlAttribute]
        public string $text = 'OK',
        #[Serializer\XmlValue(cdata: false)]
        public ?string $message = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        public ?string $lang = null,
    ) {
    }
}
