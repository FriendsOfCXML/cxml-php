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
     * @Ser\XmlElement(cdata=false)
     */
    private array $deliverTo;

    /**
     * @Ser\XmlList(inline=true, entry="Street")
     * @Ser\Type("array<string>")
     * @Ser\XmlElement(cdata=false)
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
    private array $extrinsics = [];

    public function __construct(array $deliverTo, array $street, string $city, Country $country, ?string $municipality, ?string $state, ?string $postalCode, ?string $name = null)
    {
        $this->name = $name;
        $this->deliverTo = $deliverTo;
        $this->street = $street;
        $this->city = $city;
        $this->municipality = $municipality;
        $this->state = $state;
        $this->postalCode = $postalCode;
        $this->country = $country;
    }

	public function addExtrinsic(Extrinsic $extrinsic): self
	{
		$this->extrinsics[] = $extrinsic;

		return $this;
	}
}
