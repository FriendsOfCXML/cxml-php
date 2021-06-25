<?php

namespace Mathielen\CXml\Handler;

use Assert\Assertion;
use Mathielen\CXml\Handler\Exception\CXmlHandlerNotFound;

class HandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var HandlerInterface[]
     */
    private array $registry = [];

    public function register(HandlerInterface $handler, string $handlerId = null): void
    {
        if (!$handlerId) {
            $handlerId = $handler::getRequestName();
        }

        Assertion::keyNotExists($this->registry, $handlerId, "Handler for '$handlerId' already registered.");

        $this->registry[$handlerId] = $handler;
    }

    public function all(): array
    {
        return $this->registry;
    }

    /**
     * @throws CXmlHandlerNotFound
     */
    public function get(string $handlerId): HandlerInterface
    {
        if (!isset($this->registry[$handlerId])) {
            throw new CXmlHandlerNotFound($handlerId);
        }

        return $this->registry[$handlerId];
    }
}
