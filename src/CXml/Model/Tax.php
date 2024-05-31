<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Tax
{
    #[Serializer\SerializedName('Money')]
    private Money $money;

    #[Serializer\SerializedName('Description')]
    #[Serializer\XmlElement(cdata: false)]
    private MultilanguageString $description;

    public function __construct(string $currency, int $value, MultilanguageString $description)
    {
        $this->money = new Money($currency, $value);
        $this->description = $description;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getDescription(): MultilanguageString
    {
        return $this->description;
    }
}
