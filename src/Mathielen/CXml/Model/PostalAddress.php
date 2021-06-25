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
     */
    private string $city;

    /**
     * @Ser\SerializedName("Municipality")
     */
    private ?string $municipality;

    /**
     * @Ser\SerializedName("State")
     */
    private ?string $state;

    /**
     * @Ser\SerializedName("PostalCode")
     */
    private ?string $postalCode;

    /**
     * @Ser\SerializedName("Country")
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
