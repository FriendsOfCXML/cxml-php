<?php

namespace Mathielen\CXml\Handler\Request;

use Mathielen\CXml\Handler\HandlerInterface;
use Mathielen\CXml\Handler\HandlerRegistry;
use Mathielen\CXml\Model\Header;
use Mathielen\CXml\Model\PayloadInterface;
use Mathielen\CXml\Model\Response\ProfileResponse;
use Mathielen\CXml\Model\Response\PunchoutSetupResponse;
use Mathielen\CXml\Model\ResponseInterface;
use Mathielen\CXml\Model\Transaction;
use Mathielen\CXml\Model\Url;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StaticStartPagePunchOutSetupRequestHandler implements HandlerInterface
{
    private string $startPageUrl;

    public function __construct(string $startPageUrl)
    {
        $this->startPageUrl = $startPageUrl;
    }

    public function handle(PayloadInterface $payload, ?Header $header = null): ?ResponseInterface
    {
        $punchoutSetupResponse = new PunchoutSetupResponse(
            new Url(
                $this->startPageUrl
            )
        );

        return $punchoutSetupResponse;
    }

    public static function getRequestName(): string
    {
        return 'PunchOutSetupRequest';
    }
}
