<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class AccountingSegment
{
    private function __construct(
        #[Serializer\XmlAttribute]
        public int $id,
        #[Serializer\SerializedName('Name')]
        #[Serializer\XmlElement(cdata: false)]
        public MultilanguageString $name,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        public MultilanguageString $description,
    ) {
    }
}
