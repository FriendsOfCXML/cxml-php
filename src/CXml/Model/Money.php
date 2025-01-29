<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

use function number_format;

readonly class Money
{
    #[Serializer\XmlValue(cdata: false)]
    public string $value;

    public function __construct(
        #[Serializer\XmlAttribute]
        public string $currency,
        #[Serializer\Exclude]
        public int $valueCent,
    ) {
        $this->value = number_format($this->valueCent / 100, 2, '.', '');
    }

    public function getValueCent(): int
    {
        return $this->valueCent ?? (int)(((float)$this->value) * 100);
    }
}
