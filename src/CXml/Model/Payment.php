<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Payment
{
    public function __construct(
        #[Serializer\XmlElement]
        #[Serializer\SerializedName('PaymentService')]
        private PaymentService $paymentService,
    ) {
    }

    public function getPaymentService(): PaymentService
    {
        return $this->paymentService;
    }
}
