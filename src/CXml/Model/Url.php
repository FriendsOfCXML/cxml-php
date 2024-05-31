<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Url
{
    #[Serializer\SerializedName('URL')]
    #[Serializer\XmlElement(cdata: false)]
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
