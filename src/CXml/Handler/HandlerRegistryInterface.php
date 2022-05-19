<?php

namespace CXml\Handler;

interface HandlerRegistryInterface
{
	public function get(string $handlerId): HandlerInterface;
}
