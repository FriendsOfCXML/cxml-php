<?php

namespace Mathielen\CXml\Model;

class PayloadIdentity
{
	private string $payloadId;
	private \DateTime $timestamp;

	public function __construct(string $payloadId, \DateTime $timestamp = null)
	{
		$this->payloadId = $payloadId;
		$this->timestamp = $timestamp ?? new \DateTime();
	}

	public function getPayloadId(): string
	{
		return $this->payloadId;
	}

	public function getTimestamp(): \DateTime
	{
		return $this->timestamp;
	}
}
