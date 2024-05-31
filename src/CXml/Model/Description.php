<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Description extends MultilanguageString
{
    #[Serializer\SerializedName('ShortName')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $shortName = null;

    public function __construct(?string $value, string $type = null, string $lang = 'en')
    {
        parent::__construct($value, $type, $lang);
    }

    public static function createWithShortName(string $shortName, string $type = null, string $lang = 'en'): self
    {
        $new = new self(null, $type, $lang);
        $new->setShortname($shortName);

        return $new;
    }

    public function setShortname(?string $shortName): void
    {
        $this->shortName = $shortName;
    }
}
