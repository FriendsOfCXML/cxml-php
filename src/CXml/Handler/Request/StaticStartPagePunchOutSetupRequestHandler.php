<?php

declare(strict_types=1);

namespace CXml\Handler\Request;

use CXml\Context;
use CXml\Handler\HandlerInterface;
use CXml\Model\PayloadInterface;
use CXml\Model\Response\PunchOutSetupResponse;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Model\Url;

readonly class StaticStartPagePunchOutSetupRequestHandler implements HandlerInterface
{
    public function __construct(private string $startPageUrl)
    {
    }

    public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface
    {
        return new PunchOutSetupResponse(
            new Url(
                $this->startPageUrl,
            ),
        );
    }

    public static function getRequestName(): string
    {
        return 'PunchOutSetupRequest';
    }
}
