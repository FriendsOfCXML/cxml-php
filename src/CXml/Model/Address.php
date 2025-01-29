<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['name', 'postalAddress', 'email', 'phone', 'fax', 'url'])]
readonly class Address
{
    public function __construct(
        #[Serializer\SerializedName('Name')]
        #[Serializer\XmlElement(cdata: false)]
        public MultilanguageString $name,
        #[Serializer\SerializedName('PostalAddress')]
        public ?PostalAddress $postalAddress = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('addressID')]
        public ?string $addressId = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('addressIDDomain')]
        public ?string $addressIdDomain = null,
        #[Serializer\SerializedName('Email')]
        #[Serializer\XmlElement(cdata: false)]
        public ?string $email = null,
        #[Serializer\SerializedName('Phone')]
        #[Serializer\XmlElement(cdata: false)]
        public ?Phone $phone = null,
        #[Serializer\SerializedName('Fax')]
        #[Serializer\XmlElement(cdata: false)]
        public ?string $fax = null,
        #[Serializer\SerializedName('URL')]
        #[Serializer\XmlElement(cdata: false)]
        public ?string $url = null,
    ) {
    }
}
