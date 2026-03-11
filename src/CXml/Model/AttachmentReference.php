<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class AttachmentReference
{
    public function __construct(
        #[Serializer\SerializedName('Name')]
        #[Serializer\XmlElement(cdata: false)]
        public MultilanguageString $name,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        public MultilanguageString $description,
        #[Serializer\SerializedName('InternalID')]
        #[Serializer\XmlElement(cdata: false)]
        public string $internalId,
        #[Serializer\SerializedName('URL')]
        #[Serializer\XmlElement(cdata: false)]
        public ?string $url = null,
    ) {
    }
}
