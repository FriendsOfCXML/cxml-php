<?php

declare(strict_types=1);

namespace CXml\Model;

use DateTime;
use DateTimeInterface;

readonly class PayloadIdentity
{
    private DateTimeInterface $timestamp;

    public function __construct(private string $payloadId, DateTimeInterface $timestamp = null)
    {
        $this->timestamp = $timestamp ?? new DateTime();
    }

    public function getPayloadId(): string
    {
        return $this->payloadId;
    }

    public function getTimestamp(): DateTimeInterface
    {
        return $this->timestamp;
    }
}
