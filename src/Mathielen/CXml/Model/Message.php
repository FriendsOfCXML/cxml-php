<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Message implements PayloadInterface
{
	/**
	 * @Ser\SerializedName("Status")
	 */
	private ?Status $status;

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("deploymentMode")
	 */
	private ?string $deploymentMode;

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("inReplyTo")
	 */
	private ?string $inReplyTo;

	/**
	 * @Ser\XmlAttribute
	 * @Ser\SerializedName("Id")
	 */
	private ?string $id;

	/**
	 * @Ser\Exclude
	 * see JmsEventSubscriber
	 */
	private MessageInterface $payload;

	public function __construct(
		MessageInterface $message,
		Status $status = null,
		string $id = null,
		string $deploymentMode = null,
		string $inReplyTo = null
	) {
		$this->status = $status;
		$this->payload = $message;
		$this->deploymentMode = $deploymentMode;
		$this->inReplyTo = $inReplyTo;
		$this->id = $id;
	}

	public function getStatus(): ?Status
	{
		return $this->status;
	}

	public function getDeploymentMode(): ?string
	{
		return $this->deploymentMode;
	}

	public function getInReplyTo(): ?string
	{
		return $this->inReplyTo;
	}

	public function getId(): ?string
	{
		return $this->id;
	}

	public function getPayload(): MessageInterface
	{
		return $this->payload;
	}
}
