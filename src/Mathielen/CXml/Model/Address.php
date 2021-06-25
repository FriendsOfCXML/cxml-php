<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Address
{
    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("isoCountryCode")
     */
    private ?string $isoCountryCode;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("addressID")
     */
    private ?string $addressId;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("addressIDDomain")
     */
    private ?string $addressIdDomain;

    /**
     * @Ser\SerializedName("Name")
     */
    private string $name;

    /**
     * @Ser\SerializedName("PostalAddress")
     */
    private ?PostalAddress $postalAddress;

    /**
     * @Ser\SerializedName("Email")
     */
    private ?string $email;

    /**
     * @Ser\SerializedName("Phone")
     */
    private ?string $phone;

    /**
     * @Ser\SerializedName("Fax")
     */
    private ?string $fax;

    /**
     * @Ser\SerializedName("URL")
     */
    private ?string $url;
}
