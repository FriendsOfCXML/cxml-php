<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Phone
{
    #[Serializer\SerializedName('TelephoneNumber')]
    #[Serializer\XmlElement(cdata: false)]
    private TelephoneNumber $telephoneNumber;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('name')]
    private ?string $name = null;

    public function __construct(TelephoneNumber $telephoneNumber, string $name = null)
    {
        $this->telephoneNumber = $telephoneNumber;
        $this->name = $name;
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
