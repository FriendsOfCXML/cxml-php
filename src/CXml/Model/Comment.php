<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['value', 'attachment'])]
readonly class Comment
{
    #[Serializer\SerializedName('Attachment')]
    public ?Url $attachment;

    public function __construct(
        #[Serializer\XmlValue(cdata: false)]
        public ?string $value = null,
        #[Serializer\XmlAttribute]
        public ?string $type = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        public ?string $lang = null,
        ?string $attachment = null,
    ) {
        $this->attachment = (null === $attachment ? null : new Url($attachment));
    }
}
