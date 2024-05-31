<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Address
{
    #[Ser\XmlAttribute]
    #[Ser\SerializedName('addressID')]
    private ?string $addressId = null;

    #[Ser\XmlAttribute]
    #[Ser\SerializedName('addressIDDomain')]
    private ?string $addressIdDomain = null;

    #[Ser\SerializedName('Name')]
    #[Ser\XmlElement(cdata: false)]
    private MultilanguageString $name;

    #[Ser\SerializedName('PostalAddress')]
    private ?PostalAddress $postalAddress = null;

    #[Ser\SerializedName('Email')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $email = null;

    #[Ser\SerializedName('Phone')]
    #[Ser\XmlElement(cdata: false)]
    private ?Phone $phone = null;

    #[Ser\SerializedName('Fax')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $fax = null;

    #[Ser\SerializedName('URL')]
    #[Ser\XmlElement(cdata: false)]
    private ?string $url = null;

    public function __construct(MultilanguageString $name, PostalAddress $postalAddress = null, string $addressId = null, string $addressIdDomain = null, string $email = null, Phone $phone = null, string $fax = null, string $url = null)
    {
        $this->addressId = $addressId;
        $this->addressIdDomain = $addressIdDomain;
        $this->name = $name;
        $this->postalAddress = $postalAddress;
        $this->email = $email;
        $this->phone = $phone;
        $this->fax = $fax;
        $this->url = $url;
    }

    public function getAddressId(): ?string
    {
        return $this->addressId;
    }

    public function getAddressIdDomain(): ?string
    {
        return $this->addressIdDomain;
    }

    public function getName(): MultilanguageString
    {
        return $this->name;
    }

    public function getPostalAddress(): ?PostalAddress
    {
        return $this->postalAddress;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
