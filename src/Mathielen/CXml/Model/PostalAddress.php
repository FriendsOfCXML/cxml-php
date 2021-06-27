<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class PostalAddress
{
    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("name")
     */
    private ?string $name;

    /**
     * @Ser\XmlList(inline=true, entry="DeliverTo")
     * @Ser\Type("array<string>")
     */
    private array $deliverTo;

    /**
     * @Ser\XmlList(inline=true, entry="Street")
     * @Ser\Type("array<string>")
     */
    private array $street;

    /**
     * @Ser\SerializedName("City")
	 * @Ser\XmlElement (cdata=false)
	 */
    private string $city;

    /**
     * @Ser\SerializedName("Municipality")
	 * @Ser\XmlElement (cdata=false)
	 */
    private ?string $municipality;

    /**
     * @Ser\SerializedName("State")
	 * @Ser\XmlElement (cdata=false)
	 */
    private ?string $state;

    /**
     * @Ser\SerializedName("PostalCode")
	 * @Ser\XmlElement (cdata=false)
	 */
    private ?string $postalCode;

    /**
     * @Ser\SerializedName("Country")
	 * @Ser\XmlElement (cdata=false)
	 */
    private Country $country;

    /**
     * @Ser\XmlList(inline=true, entry="Extrinsic")
     * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
     *
     * @var Extrinsic[]
     */
    private array $extrinsics;
}
