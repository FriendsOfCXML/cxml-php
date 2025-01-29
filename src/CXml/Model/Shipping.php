<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['money', 'description'])]
readonly class Shipping
{
    #[Serializer\SerializedName('Money')]
    public Money $money;

    public function __construct(
        string $currency,
        int $value,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        public Description $description,
    ) {
        $this->money = new Money($currency, $value);
    }
}
