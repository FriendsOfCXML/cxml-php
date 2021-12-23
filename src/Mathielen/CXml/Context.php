<?php

namespace Mathielen\CXml;

use Mathielen\CXml\Model\CXml;

class Context
{
	private ?CXml $cxml = null;
	private bool $dryrun = false;

	private function __construct()
	{
	}

	public static function create(): self
	{
		return new self();
	}

	public function setDryRun(bool $dryrun): self
	{
		$this->dryrun = $dryrun;

		return $this;
	}

	public function isDryRun(): bool
	{
		return $this->dryrun;
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
