<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class AccountingSegment
{
    private function __construct(
        #[Serializer\XmlAttribute]
        private int $id,
        #[Serializer\SerializedName('Name')]
        #[Serializer\XmlElement(cdata: false)]
        private MultilanguageString $name,
        #[Serializer\SerializedName('Description')]
        #[Serializer\XmlElement(cdata: false)]
        private MultilanguageString $description,
    ) {
    }
}
