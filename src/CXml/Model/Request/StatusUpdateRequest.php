<?php

namespace CXml\Model\Request;

use CXml\Model\DocumentReference;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\Status;
use JMS\Serializer\Annotation as Ser;

class StatusUpdateRequest implements RequestPayloadInterface
{
	use ExtrinsicsTrait;

	/**
	 * @Ser\SerializedName("DocumentReference")
	 */
	private ?DocumentReference $documentReference = null;

	/**
	 * @Ser\SerializedName("Status")
	 */
	private Status $status;

	public function __construct(Status $status, string $documentReference = null)
	{
		$this->status = $status;
		$this->documentReference = $documentReference ? new DocumentReference($documentReference) : null;
	}

	public static function create(Status $status, string $documentReference = null): self
	{
		return new self(
			$status,
			$documentReference
		);
	}

	public function getDocumentReference(): ?DocumentReference
	{
		return $this->documentReference;
	}

	public function getStatus(): Status
	{
		return $this->status;
	}
}
