<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Money
{
    #[Serializer\XmlAttribute]
    private string $currency;

    #[Serializer\XmlValue(cdata: false)]
    private string $value;

    #[Serializer\Exclude]
    private int $valueCent;

    public function __construct(string $currency, int $valueCent)
    {
        $this->currency = $currency;
        $this->valueCent = $valueCent;
        $this->value = \number_format($valueCent / 100, 2, '.', '');
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getValueCent(): int
    {
        return $this->valueCent ?? (int) (((float) $this->value) * 100);
    }
}
