<?php

declare(strict_types=1);

namespace CXmlTest\Handler;

use CXml\Context;
use CXml\Exception\CXmlNotImplementedException;
use CXml\Handler\Exception\CXmlHandlerNotFoundException;
use CXml\Handler\HandlerRegistry;
use CXml\Handler\Request\SelfAwareProfileRequestHandler;
use CXml\Handler\Request\StaticStartPagePunchOutSetupRequestHandler;
use CXml\Model\Request\ProfileRequest;
use CXml\Model\Response\ProfileResponse;
use CXml\Model\Response\PunchOutSetupResponse;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * @internal
 */
#[CoversNothing]
final class HandlerRegistryTest extends TestCase
{
    public function testRegisterUsesRequestName(): void
    {
        $handler = new StaticStartPagePunchOutSetupRequestHandler('https://example.com');
        $registry = new HandlerRegistry();

        $registry->register($handler);

        $this->assertSame($handler, $registry->get('PunchOutSetupRequest'));
        $this->assertSame(['PunchOutSetupRequest' => $handler], $registry->all());
    }

    public function testRegisterWithCustomId(): void
    {
        $handler = new StaticStartPagePunchOutSetupRequestHandler('https://example.com');
        $registry = new HandlerRegistry();

        $registry->register($handler, 'Custom');
        $registry->register($handler, '');

        $this->assertSame($handler, $registry->get('Custom'));
        $this->assertSame($handler, $registry->get('PunchOutSetupRequest'), 'empty id falls back to the request name');
    }

    public function testRegisterDuplicate(): void
    {
        $registry = new HandlerRegistry();
        $registry->register(new StaticStartPagePunchOutSetupRequestHandler('https://example.com'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Handler for 'PunchOutSetupRequest' already registered.");

        $registry->register(new StaticStartPagePunchOutSetupRequestHandler('https://example.org'));
    }

    public function testGetUnknown(): void
    {
        try {
            (new HandlerRegistry())->get('OrderRequest');
            $this->fail('Expected CXmlHandlerNotFoundException');
        } catch (CXmlHandlerNotFoundException $cXmlHandlerNotFoundException) {
            $this->assertInstanceOf(CXmlNotImplementedException::class, $cXmlHandlerNotFoundException);
            $this->assertSame('Handler for OrderRequest not found. Register first.', $cXmlHandlerNotFoundException->getMessage());
        }
    }

    public function testStaticStartPageHandler(): void
    {
        $handler = new StaticStartPagePunchOutSetupRequestHandler('https://example.com/start');

        $response = $handler->handle(new ProfileRequest(), Context::create());

        $this->assertInstanceOf(PunchOutSetupResponse::class, $response);
        $this->assertSame('https://example.com/start', $response->startPage->url);
    }

    public function testSelfAwareProfileRequestHandler(): void
    {
        $urlGenerator = new class implements UrlGeneratorInterface {
            /**
             * @var array<array{string, int}>
             */
            public array $calls = [];

            public function setContext(RequestContext $context): void
            {
            }

            public function getContext(): RequestContext
            {
                return new RequestContext();
            }

            public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
            {
                $this->calls[] = [$name, $referenceType];

                return 'https://supplier.example.com/cxml';
            }
        };

        $registry = new HandlerRegistry();
        $profileRequestHandler = new SelfAwareProfileRequestHandler($registry, $urlGenerator, 'my_route');
        $registry->register($profileRequestHandler);
        $registry->register(new StaticStartPagePunchOutSetupRequestHandler('https://example.com'));

        $response = $profileRequestHandler->handle(new ProfileRequest(), Context::create());

        $this->assertInstanceOf(ProfileResponse::class, $response);
        $this->assertSame([['my_route', UrlGeneratorInterface::ABSOLUTE_URL], ['my_route', UrlGeneratorInterface::ABSOLUTE_URL]], $urlGenerator->calls);

        $transactions = (fn (): array => $this->transactions)->call($response);
        $this->assertCount(2, $transactions);
        $this->assertSame('ProfileRequest', $transactions[0]->requestName);
        $this->assertSame('PunchOutSetupRequest', $transactions[1]->requestName);
        $this->assertSame('https://supplier.example.com/cxml', $transactions[1]->url);
    }
}
