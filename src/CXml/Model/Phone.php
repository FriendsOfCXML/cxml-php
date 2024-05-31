<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Phone
{
    #[Ser\SerializedName('TelephoneNumber')]
    #[Ser\XmlElement(cdata: false)]
    private TelephoneNumber $telephoneNumber;

    #[Ser\XmlAttribute]
    #[Ser\SerializedName('name')]
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
