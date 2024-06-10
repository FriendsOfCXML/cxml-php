<?php

declare(strict_types=1);

namespace CXml;

use CXml\Model\CXml;
use CXml\Model\Header;

class Context
{
    private ?CXml $cxml = null;

    private function __construct(private array $options = [])
    {
    }

    public static function create(array $options = []): self
    {
        return new self($options);
    }

    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setDryRun(bool $dryrun): self
    {
        $this->options['dryrun'] = $dryrun;

        return $this;
    }

    public function isDryRun(): bool
    {
        return $this->options['dryrun'] ?? false;
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
        $cxml = $this->getCXml();
        if (!$cxml instanceof CXml) {
            return null;
        }

        $header = $cxml->getHeader();
        if (!$header instanceof Header) {
            return null;
        }

        return $header->getSender()->getUserAgent();
    }

    public function getPayloadId(): ?string
    {
        $cxml = $this->getCXml();
        if (!$cxml instanceof CXml) {
            return null;
        }

        return $cxml->getPayloadId();
    }
}
