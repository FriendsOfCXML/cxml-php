<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Payment
{
    public function __construct(
        /* this is being dynamically serialized with CXmlWrappingNodeJmsEventSubscriber */
        #[Serializer\Exclude]
        public PaymentInterface $paymentImpl,
    ) {
    }
}
