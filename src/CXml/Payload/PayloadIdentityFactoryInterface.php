<?php

namespace CXml\Payload;

use CXml\Model\PayloadIdentity;

interface PayloadIdentityFactoryInterface
{
	public function newPayloadIdentity(): PayloadIdentity;
}
