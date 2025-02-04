<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['telephoneNumber'])]
readonly class Phone
{
    public function __construct(
        #[Serializer\SerializedName('TelephoneNumber')]
        #[Serializer\XmlElement(cdata: false)]
        public TelephoneNumber $telephoneNumber,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('name')]
        public ?string $name = null,
    ) {
    }

    public function getTelephoneNumberAsString(): ?string
    {
        $countryCode = $this->telephoneNumber->countryCode->dialCode ?? '';
        $areaOrCityCode = $this->telephoneNumber->areaOrCityCode;
        $telephoneNumber = $this->telephoneNumber->number;
        $extension = null === $this->telephoneNumber->extension ? '' : ' -' . $this->telephoneNumber->extension;

        if ('' === $telephoneNumber) {
            return null;
        }

        return sprintf('+%s (%s) %s%s', $countryCode, $areaOrCityCode, $telephoneNumber, $extension);
    }
}
