<?php

namespace Mathielen\CXml\Payload;

use Mathielen\CXml\Model\PayloadIdentity;

interface PayloadIdentityFactoryInterface
{
	public function newPayloadIdentity(): PayloadIdentity;
}
