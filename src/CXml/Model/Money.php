<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

use function number_format;
use function round;

readonly class Money
{
    #[Serializer\XmlValue(cdata: false)]
    public string $value;

    public function __construct(
        #[Serializer\XmlAttribute]
        public string $currency,
        #[Serializer\Exclude]
        public ?int $valueCent = null,
    ) {
        $this->value = number_format((int)$this->valueCent / 100, 2, '.', '');
    }

    public function getValueCent(): int
    {
        // round instead of truncating, as i.e. 19.99 * 100 results in 1998.9999999999998
        return $this->valueCent ?? (int)round(((float)$this->value) * 100);
    }
}
