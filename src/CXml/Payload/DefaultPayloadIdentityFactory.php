<?php

namespace CXml\Payload;

use CXml\Model\PayloadIdentity;

class DefaultPayloadIdentityFactory implements PayloadIdentityFactoryInterface
{
    /**
     * @var callable|\Closure
     */
    private $timeCallable;

    public function __construct(callable $timeCallable = null)
    {
        $this->timeCallable = $timeCallable ?? function () {
            return new \DateTime();
        };
    }

    private static function generateNewPayloadId(\DateTimeInterface $timestamp): string
    {
        // The recommended implementation is:
        // datetime.process id.random number@hostname
        return \sprintf(
            '%s.%s.%s@%s',
            $timestamp->format('U.v'), // include milliseconds
            \getmypid(),
            \mt_rand(1000, 9999),
            \gethostname()
        );
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        /** @var \DateTimeInterface $timestamp */
        $timestamp = \call_user_func($this->timeCallable);
        $payloadId = self::generateNewPayloadId($timestamp);

        return new PayloadIdentity(
            $payloadId,
            $timestamp
        );
    }
}
