<?php

namespace CXml;

use CXml\Model\CXml;

class Context
{
	private ?CXml $cxml = null;
	private array $options;

	private function __construct(array $options = [])
	{
		$this->options = $options;
	}

	public static function create(array $options = []): self
	{
		return new self($options);
	}

	public function getOption(string $key)/*: mixed*/
	{
		return $this->options[$key] ?? null;
	}

	public function setDryRun(bool $dryrun): self
	{
		$this->options['dryrun'] = $dryrun;

		return $this;
	}

	public function isDryRun(): bool
	{
		return $this->options['dryrun'];
	}

	public function getCXml(): ?CXml
	{
		return $this->cxml;
	}

	public function setCXml(CXml $cxml): self
	{
		$this->cxml = $cxml;

		return $this;
	}

	public function getSenderUserAgent(): ?string
	{
		$cxml = $this->getCxml();
		if (!$cxml) {
			return null;
		}

		$header = $cxml->getHeader();
		if (!$header) {
			return null;
		}

		return $header->getSender()->getUserAgent();
	}

	public function getPayloadId(): ?string
	{
		$cxml = $this->getCxml();
		if (!$cxml) {
			return null;
		}

		return $cxml->getPayloadId();
	}
}
