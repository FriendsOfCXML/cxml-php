<?php

namespace Mathielen\CXml\Handler;

interface HandlerRegistryInterface
{
	public function get(string $handlerId): HandlerInterface;
}
