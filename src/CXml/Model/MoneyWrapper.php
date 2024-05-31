<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class MoneyWrapper
{
    #[Ser\SerializedName('Money')]
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
