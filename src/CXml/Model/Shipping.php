<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Shipping
{
    #[Serializer\SerializedName('Money')]
    private Money $money;

    #[Serializer\SerializedName('Description')]
    #[Serializer\XmlElement(cdata: false)]
    private Description $description;

    public function __construct(string $currency, int $value, Description $description)
    {
        $this->money = new Money($currency, $value);
        $this->description = $description;
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
