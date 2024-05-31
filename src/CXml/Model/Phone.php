<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['telephoneNumber'])]
readonly class Phone
{
    public function __construct(
        #[Serializer\SerializedName('TelephoneNumber')]
        #[Serializer\XmlElement(cdata: false)]
        private TelephoneNumber $telephoneNumber,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('name')]
        private ?string $name = null
    ) {
    }

    public function getTelephoneNumber(): TelephoneNumber
    {
        return $this->telephoneNumber;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
