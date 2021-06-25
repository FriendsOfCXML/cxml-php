<?php

namespace Mathielen\CXml\Handler\Request;

use Mathielen\CXml\Handler\HandlerInterface;
use Mathielen\CXml\Handler\HandlerRegistry;
use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\Response\ProfileResponse;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Model\Transaction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SelfAwareProfileRequestHandler implements HandlerInterface
{
    private HandlerRegistry $handlerRegistry;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(HandlerRegistry $handlerRegistry, UrlGeneratorInterface $urlGenerator)
    {
        $this->handlerRegistry = $handlerRegistry;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(PayloadInterface $payload, ?Header $header = null): ?ResponseInterface
    {
        $profileResponse = new ProfileResponse();

        foreach ($this->handlerRegistry->all() as $requestName => $handler) {
            $transaction = new Transaction($requestName, $this->getEndpointUrl());

            $profileResponse->addTransaction($transaction);
        }

        return $profileResponse;
    }

    //TODO probably should be moved to HandlerInterface for full-fledged routing solution
    private function getEndpointUrl(): string
    {
        return $this->urlGenerator->generate('post_cxml', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public static function getRequestName(): string
    {
        return 'ProfileRequest';
    }
}
