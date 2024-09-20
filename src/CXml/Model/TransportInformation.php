<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['route', 'shippingContractNumber'])]
readonly class TransportInformation
{
    public function __construct(
        #[Serializer\SerializedName('Route')]
        private ?Route $route,
        #[Serializer\SerializedName('ShippingContractNumber')]
        private ?ShippingContractNumber $shippingContractNumber,
    ) {
    }

    public static function create(?string $carrierAccountNo = null, ?string $carrierShippingMethod = null): self
    {
        return new self(
            null === $carrierShippingMethod ? null : new Route($carrierShippingMethod),
            null === $carrierAccountNo ? null : new ShippingContractNumber($carrierAccountNo),
        );
    }
}
