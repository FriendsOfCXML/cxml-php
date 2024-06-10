<?php

declare(strict_types=1);

namespace CXml\Handler\Request;

use CXml\Context;
use CXml\Handler\HandlerInterface;
use CXml\Handler\HandlerRegistry;
use CXml\Model\PayloadInterface;
use CXml\Model\Response\ProfileResponse;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Model\Transaction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function array_keys;

readonly class SelfAwareProfileRequestHandler implements HandlerInterface
{
    public function __construct(private HandlerRegistry $handlerRegistry, private UrlGeneratorInterface $urlGenerator, private string $defaultRoute = 'post_cxml')
    {
    }

    public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface
    {
        $profileResponse = new ProfileResponse();

        foreach (array_keys($this->handlerRegistry->all()) as $requestName) {
            $transaction = new Transaction($requestName, $this->getEndpointUrl());

            $profileResponse->addTransaction($transaction);
        }

        return $profileResponse;
    }

    private function getEndpointUrl(): string
    {
        return $this->urlGenerator->generate($this->defaultRoute, [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public static function getRequestName(): string
    {
        return 'ProfileRequest';
    }
}
