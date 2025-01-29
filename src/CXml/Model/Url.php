<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Url
{
    public function __construct(
        #[Serializer\SerializedName('URL')]
        #[Serializer\XmlElement(cdata: false)]
        public string $url,
    ) {
    }
}
