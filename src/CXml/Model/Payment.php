<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Payment
{
    public function __construct(
        /* this is being dynamically serialized with CXmlWrappingNodeJmsEventSubscriber */
        #[Serializer\Exclude]
        private PaymentInterface $paymentImpl,
    ) {
    }

    public function getPaymentImpl(): PaymentInterface
    {
        return $this->paymentImpl;
    }
}
