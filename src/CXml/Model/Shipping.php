<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['money', 'description'])]
readonly class Shipping
{
    #[Serializer\SerializedName('Money')]
    private Money $money;

    public function __construct(
        string $currency,
        int $value,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        private Description $description
    ) {
        $this->money = new Money($currency, $value);
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getDescription(): ?MultilanguageString
    {
        return $this->description;
    }
}
