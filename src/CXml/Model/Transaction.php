<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Transaction
{
    #[Serializer\XmlAttribute]
    private string $requestName;

    #[Serializer\SerializedName('URL')]
    #[Serializer\XmlElement(cdata: false)]
    private string $url;

    /**
     * @var Option[]
     */
    #[Serializer\XmlList(inline: true, entry: 'Option')]
    #[Serializer\Type('array<CXml\Model\Option>')]
    private array $options = [];

    public function __construct(string $requestName, string $url)
    {
        $this->requestName = $requestName;
        $this->url = $url;
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }
}
