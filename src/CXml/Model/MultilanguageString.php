<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class MultilanguageString
{
    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        private readonly ?string $value,
        #[Serializer\XmlAttribute]
        private readonly ?string $type = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        private readonly ?string $lang = 'en'
    ) {
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }
}
