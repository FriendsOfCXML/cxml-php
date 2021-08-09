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

	public function getCxml(): ?CXml
	{
		return $this->cxml;
	}

	public function setCXml(CXml $cxml): self
	{
		$this->cxml = $cxml;

		return $this;
	}
}
