<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['shippingContractNumber'])]
readonly class TransportInformation
{
    public function __construct(
        #[Serializer\SerializedName('ShippingContractNumber')]
        private ?ShippingContractNumber $shippingContractNumber
    ) {
    }

    public static function fromContractAccountNumber(string $carrierAccountNo): self
    {
        return new self(new ShippingContractNumber($carrierAccountNo));
    }
}
