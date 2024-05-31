<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class PostalAddress
{
    use ExtrinsicsTrait;

    #[Serializer\XmlAttribute]
    #[Serializer\SerializedName('name')]
    private ?string $name = null;

    #[Serializer\XmlList(inline: true, entry: 'DeliverTo')]
    #[Serializer\Type('array<string>')]
    #[Serializer\XmlElement(cdata: false)]
    private array $deliverTo;

    #[Serializer\XmlList(inline: true, entry: 'Street')]
    #[Serializer\Type('array<string>')]
    #[Serializer\XmlElement(cdata: false)]
    private array $street;

    #[Serializer\SerializedName('City')]
    #[Serializer\XmlElement(cdata: false)]
    private string $city;

    #[Serializer\SerializedName('Municipality')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $municipality = null;

    #[Serializer\SerializedName('State')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $state = null;

    #[Serializer\SerializedName('PostalCode')]
    #[Serializer\XmlElement(cdata: false)]
    private ?string $postalCode = null;

    #[Serializer\SerializedName('Country')]
    #[Serializer\XmlElement(cdata: false)]
    private Country $country;

    public function __construct(array $deliverTo, array $street, string $city, Country $country, string $municipality = null, string $state = null, string $postalCode = null, string $name = null)
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDeliverTo(): array
    {
        return $this->deliverTo;
    }

    public function getStreet(): array
    {
        return $this->street;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getMunicipality(): ?string
    {
        return $this->municipality;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * Convenience method to detect empty-string filled addresses.
     */
    public function isEmpty(): bool
    {
        return
            (null === $this->name || '' === $this->name || '0' === $this->name)
            && [] === \array_filter($this->deliverTo)
            && [] === \array_filter($this->street)
            && ('' === $this->city || '0' === $this->city)
            && (null === $this->municipality || '' === $this->municipality || '0' === $this->municipality)
            && (null === $this->state || '' === $this->state || '0' === $this->state)
            && (null === $this->postalCode || '' === $this->postalCode || '0' === $this->postalCode);
    }
}
