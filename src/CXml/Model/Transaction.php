<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['url', 'options'])]
class Transaction
{
    /**
     * @var Option[]
     */
    #[Serializer\XmlList(entry: 'Option', inline: true)]
    #[Serializer\Type('array<CXml\Model\Option>')]
    private array $options = [];

    public function __construct(
        #[Serializer\XmlAttribute]
        private readonly string $requestName,
        #[Serializer\SerializedName('URL')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly string $url
    ) {
    }

    public function addOption(Option $option): void
    {
        $this->options[] = $option;
    }
}
