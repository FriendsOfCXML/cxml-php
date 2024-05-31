<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Address
{
    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('addressID')]
    private ?string $addressId = null;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('addressIDDomain')]
    private ?string $addressIdDomain = null;

    #[Serializer\SerializedName('Name')]
    #[Serializer\XmlElement(cdata: false)]
    private MultilanguageString $name;

    #[Serializer\SerializedName('PostalAddress')]
    private ?PostalAddress $postalAddress = null;

    #[Serializer\SerializedName('Email')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $email = null;

    #[Serializer\SerializedName('Phone')]
    #[Serializer\XmlElement(cdata: false)]
    private ?Phone $phone = null;

    #[Serializer\SerializedName('Fax')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $fax = null;

    #[Serializer\SerializedName('URL')]
    #[Serializer\XmlElement(cdata: false)]
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
