<?php

declare(strict_types=1);

namespace CXml\Payload;

use Closure;
use CXml\Model\PayloadIdentity;
use DateTime;
use DateTimeInterface;

use function call_user_func;
use function gethostname;
use function getmypid;
use function mt_rand;
use function sprintf;

class DefaultPayloadIdentityFactory implements PayloadIdentityFactoryInterface
{
    /**
     * @var callable|Closure
     */
    private $timeCallable;

    public function __construct(?callable $timeCallable = null)
    {
        $this->timeCallable = $timeCallable ?? static fn (): DateTime => new DateTime();
    }

    private function generateNewPayloadId(DateTimeInterface $timestamp): string
    {
        // The recommended implementation is:
        // datetime.process id.random number@hostname
        return sprintf(
            '%s.%s.%s@%s',
            $timestamp->format('U.v'), // include milliseconds
            getmypid(),
            mt_rand(1000, 9999),
            gethostname(),
        );
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        /** @var DateTimeInterface $timestamp */
        $timestamp = call_user_func($this->timeCallable);
        $payloadId = $this->generateNewPayloadId($timestamp);

        return new PayloadIdentity(
            $payloadId,
            $timestamp,
        );
    }
}
