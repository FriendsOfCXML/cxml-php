<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTime;
use DateTimeInterface;

readonly class PayloadIdentity
{
    public DateTimeInterface $timestamp;

    public function __construct(
        public string $payloadId,
        ?DateTimeInterface $timestamp = null,
    ) {
        $this->timestamp = $timestamp ?? new DateTime();
    }
}
