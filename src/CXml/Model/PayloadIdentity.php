<?php

namespace CXml\Model;

readonly class PayloadIdentity
{
    private \DateTimeInterface $timestamp;

    public function __construct(private string $payloadId, \DateTimeInterface $timestamp = null)
    {
        $this->timestamp = $timestamp ?? new \DateTime();
    }

    public function getPayloadId(): string
    {
        return $this->payloadId;
    }

    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }
}
