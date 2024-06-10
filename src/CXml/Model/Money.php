<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

use function number_format;

readonly class Money
{
    #[Serializer\XmlValue(cdata: false)]
    private string $value;

    public function __construct(
        #[Serializer\XmlAttribute]
        private string $currency,
        #[Serializer\Exclude]
        private int $valueCent,
    ) {
        $this->value = number_format($this->valueCent / 100, 2, '.', '');
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
        return $this->valueCent ?? (int)(((float)$this->value) * 100);
    }
}
