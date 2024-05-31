<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class TransportInformation
{
    #[Serializer\SerializedName('ShippingContractNumber')]
    private ?ShippingContractNumber $shippingContractNumber = null;

    public function __construct(?ShippingContractNumber $shippingContractNumber)
    {
        $this->shippingContractNumber = $shippingContractNumber;
    }

    public static function fromContractAccountNumber(string $carrierAccountNo): self
    {
        return new self(new ShippingContractNumber($carrierAccountNo));
    }
}
