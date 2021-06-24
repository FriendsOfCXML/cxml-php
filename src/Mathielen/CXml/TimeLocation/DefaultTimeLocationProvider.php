<?php

namespace Mathielen\CXml\TimeLocation;

use Mathielen\CXml\Model\PayloadIdentity;
use Mathielen\CXml\TimeLocation\TimeLocationProviderInterface;

class DefaultTimeLocationProvider implements TimeLocationProviderInterface
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
