<?php

declare(strict_types=1);

namespace CXml\Payload;

use CXml\Model\PayloadIdentity;

interface PayloadIdentityFactoryInterface
{
    public function newPayloadIdentity(): PayloadIdentity;
}
