<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value', 'shortName'])]
readonly class Description extends MultilanguageString
{
    #[Serializer\SerializedName('ShortName')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $shortName;

    public function __construct(?string $value, ?string $type = null, string $lang = 'en', ?string $shortName = null)
    {
        parent::__construct($value, $type, $lang);

        $this->shortName = $shortName;
    }

    public static function createWithShortName(string $shortName, ?string $type = null, string $lang = 'en'): self
    {
        return new self(null, $type, $lang, $shortName);
    }
}
