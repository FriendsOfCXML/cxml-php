<?php

namespace Mathielen\CXml\Payload;

use Mathielen\CXml\Model\PayloadIdentity;

class DefaultPayloadIdentityFactory implements PayloadIdentityFactoryInterface
{
    private static function generateNewPayloadId(\DateTime $timestamp): string
    {
        //The recommended implementation is:
        //datetime.process id.random number@hostname
        return sprintf(
            '%s.%s.%s@%s',
            $timestamp->getTimestamp(),
            getmypid(),
            rand(1000, 9999),
            gethostname()
        );
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        $timestamp = new \DateTime();
        $payloadId = self::generateNewPayloadId($timestamp);

        return new PayloadIdentity(
            $payloadId,
            $timestamp
        );
    }
}
