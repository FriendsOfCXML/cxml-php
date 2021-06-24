<?php

namespace Mathielen\CXml\TimeLocation;

use Mathielen\CXml\Model\PayloadIdentity;

interface TimeLocationProviderInterface
{
    public function newPayloadIdentity(): PayloadIdentity;
}
