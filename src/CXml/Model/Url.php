<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Url
{
    public function __construct(
        #[Serializer\SerializedName('URL')]
        #[Serializer\XmlElement(cdata: false)]
        private string $url
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
