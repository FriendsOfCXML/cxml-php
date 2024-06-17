<?php

declare(strict_types=1);

namespace CXml\Handler;

interface HandlerRegistryInterface
{
    public function get(string $handlerId): HandlerInterface;
}
