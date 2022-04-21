<?php

namespace Mathielen\CXml\Model\Request;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\DocumentReference;
use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\ExtrinsicsTrait;
use Mathielen\CXml\Model\RequestInterface;
use Mathielen\CXml\Model\Status;

class StatusUpdateRequest implements RequestInterface
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

	public function __construct(Status $status, ?string $documentReference = null)
	{
		$this->status = $status;
		$this->documentReference = $documentReference ? new DocumentReference($documentReference) : null;
	}

	public static function create(Status $status, ?string $documentReference = null): self
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
