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
        private TelephoneNumber $telephoneNumber,
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('name')]
        private ?string $name = null,
    ) {
    }

    public function getTelephoneNumber(): TelephoneNumber
    {
        return $this->telephoneNumber;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getTelephoneNumberAsString(): ?string
    {
        $countryCode = $this->telephoneNumber->getCountryCode()->getDialCode() ?? '';
        $areaOrCityCode = $this->telephoneNumber->getAreaOrCityCode();
        $telephoneNumber = $this->telephoneNumber->getNumber();
        $extension = null === $this->telephoneNumber->getExtension() ? '' : ' -' . $this->telephoneNumber->getExtension();

        if ($telephoneNumber === '') {
            return null;
        }

        return sprintf('+%s (%s) %s%s', $countryCode, $areaOrCityCode, $telephoneNumber, $extension);
    }
}
