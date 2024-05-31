<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value', 'attachment'])]
readonly class Comment
{
    #[Serializer\SerializedName('Attachment')]
    private ?Url $attachment;

    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        private ?string $value = null,
        #[Serializer\XmlAttribute]
        private ?string $type = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        private ?string $lang = null,
        string $attachment = null
    ) {
        $this->attachment = $attachment ? new Url($attachment) : null;
    }

    public function getAttachment(): ?Url
    {
        return $this->attachment;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
