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
	 * @Ser\XmlElement (cdata=false)
     */
    private string $name;

    /**
     * @Ser\SerializedName("PostalAddress")
     */
    private ?PostalAddress $postalAddress;

    /**
     * @Ser\SerializedName("Email")
	 * @Ser\XmlElement (cdata=false)
     */
    private ?string $email;

    /**
     * @Ser\SerializedName("Phone")
	 * @Ser\XmlElement (cdata=false)
	 */
    private ?string $phone;

    /**
     * @Ser\SerializedName("Fax")
	 * @Ser\XmlElement (cdata=false)
	 */
    private ?string $fax;

    /**
     * @Ser\SerializedName("URL")
	 * @Ser\XmlElement (cdata=false)
	 */
    private ?string $url;
}
