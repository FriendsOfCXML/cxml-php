<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['deliverTo', 'street', 'city', 'municipality', 'state', 'postalCode', 'country', 'extrinsics'])]
class PostalAddress
{
    use ExtrinsicsTrait;

    public function __construct(
        #[Serializer\XmlList(entry: 'DeliverTo', inline: true)]
        #[Serializer\Type('array<string>')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly array $deliverTo,
        #[Serializer\XmlList(entry: 'Street', inline: true)]
        #[Serializer\Type('array<string>')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly array $street,
        #[Serializer\SerializedName('City')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly string $city,
        #[Serializer\SerializedName('Country')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly Country $country,
        #[Serializer\SerializedName('Municipality')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly ?string $municipality = null,
        #[Serializer\SerializedName('State')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly ?string $state = null,
        #[Serializer\SerializedName('PostalCode')]
        #[Serializer\XmlElement(cdata: false)]
        private readonly ?string $postalCode = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('name')]
        private readonly ?string $name = null,
    ) {
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
