<?php

declare(strict_types=1);

namespace CXml\Handler;

use Assert\Assertion;
use CXml\Handler\Exception\CXmlHandlerNotFoundException;

use function sprintf;

class HandlerRegistry implements HandlerRegistryInterface
{
    /**
     * @var HandlerInterface[]
     */
    private array $registry = [];

    public function register(HandlerInterface $handler, ?string $handlerId = null): void
    {
        if (null === $handlerId || '' === $handlerId || '0' === $handlerId) {
            $handlerId = $handler::getRequestName();
        }

        Assertion::keyNotExists($this->registry, $handlerId, sprintf("Handler for '%s' already registered.", $handlerId));

        $this->registry[$handlerId] = $handler;
    }

    public function all(): array
    {
        return $this->registry;
    }

    /**
     * @throws CXmlHandlerNotFoundException
     */
    public function get(string $handlerId): HandlerInterface
    {
        if (!isset($this->registry[$handlerId])) {
            throw new CXmlHandlerNotFoundException($handlerId);
        }

        return $this->registry[$handlerId];
    }
}
