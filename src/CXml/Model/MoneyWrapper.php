<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class MoneyWrapper
{
    #[Serializer\SerializedName('Money')]
    private Money $money;

    public function __construct(string $currency, int $value)
    {
        $this->money = new Money($currency, $value);
    }

    public function getMoney(): Money
    {
        return $this->money;
    }
}
