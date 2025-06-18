<?php

declare(strict_types=1);

namespace CXml\Model;

use CXml\Model\Trait\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

use function array_filter;

#[Serializer\AccessorOrder(order: 'custom', custom: ['deliverTo', 'street', 'city', 'municipality', 'state', 'postalCode', 'country', 'extrinsics'])]
class PostalAddress
{
    use ExtrinsicsTrait;

    public function __construct(
        #[Serializer\XmlList(entry: 'DeliverTo', inline: true)]
        #[Serializer\Type('array<string>')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly array $deliverTo,
        #[Serializer\XmlList(entry: 'Street', inline: true)]
        #[Serializer\Type('array<string>')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly array $street,
        #[Serializer\SerializedName('City')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly string $city,
        #[Serializer\SerializedName('Country')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly Country $country,
        #[Serializer\SerializedName('Municipality')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly ?string $municipality = null,
        #[Serializer\SerializedName('State')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly ?string $state = null,
        #[Serializer\SerializedName('PostalCode')]
        #[Serializer\XmlElement(cdata: false)]
        public readonly ?string $postalCode = null,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('name')]
        public readonly ?string $name = null,
    ) {
    }

    /**
     * Convenience method to detect empty-string filled addresses.
     */
    public function isEmpty(): bool
    {
        return
            (null === $this->name || '' === $this->name || '0' === $this->name)
            && [] === array_filter($this->deliverTo, function ($value): bool {return '' !== $value && '0' !== $value; })
            && [] === array_filter($this->street, function ($value): bool {return '' !== $value && '0' !== $value; })
            && ('' === $this->city || '0' === $this->city)
            && (null === $this->municipality || '' === $this->municipality || '0' === $this->municipality)
            && (null === $this->state || '' === $this->state || '0' === $this->state)
            && (null === $this->postalCode || '' === $this->postalCode || '0' === $this->postalCode);
    }
}
