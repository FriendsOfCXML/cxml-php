<?php

namespace CXmlTest\Handling;

use CXml\Authentication\SimpleSharedSecretAuthenticator;
use CXml\Builder;
use CXml\Context;
use CXml\Credential\Registry;
use CXml\Endpoint;
use CXml\Handler\HandlerInterface;
use CXml\Handler\HandlerRegistry;
use CXml\Model\Credential;
use CXml\Model\PayloadInterface;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Processor\HeaderProcessor;
use CXml\Processor\Processor;
use CXml\Serializer;
use CXml\Validation\DtdValidator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function file_get_contents;

/**
 * @internal
 */
#[CoversNothing]
final class HandlerTest extends TestCase
{
    public static function getEndpointData(): iterable
    {
        yield [
            self::loadFixture('quote_request.xml'),
            'QuoteMessage',
        ];

        yield [
            self::loadFixture('order_request.xml'),
            'OrderRequest',
        ];

        // TODO add more cases
    }

    private static function loadFixture(string $filename): ?string
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $filename);
    }

    #[DataProvider('getEndpointData')]
    public function testEndpoint(string $requestCxml, string $expectedHandlerCalled): void
    {
        $serializer = Serializer::create();
        $messageValidator = DtdValidator::fromDtdDirectory(__DIR__ . '/../../metadata/cxml/dtd/1.2.050');

        $credentialRepository = new Registry();
        $credentialRepository->registerCredential(
            new Credential(
                'NetworkId',
                'AN00000123',
            ),
        );
        $credentialRepository->registerCredential(
            new Credential(
                'NetworkId',
                'AN00000456',
            ),
        );

        $authenticator = new SimpleSharedSecretAuthenticator('Secret!123');

        $requestProcessor = new HeaderProcessor(
            $credentialRepository,
            $authenticator,
        );

        $actualHandlerCalled = '(none)';

        $quoteMessageHandler = new class($actualHandlerCalled) implements HandlerInterface {
            public function __construct(private string &$actualHandlerCalled)
            {
            }

            public static function getRequestName(): string
            {
                return 'QuoteMessage';
            }

            public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface
            {
                $this->actualHandlerCalled = 'QuoteMessage';

                return null;
            }
        };

        $orderRequestHandler = new class($actualHandlerCalled) implements HandlerInterface {
            public function __construct(private string &$actualHandlerCalled)
            {
            }

            public static function getRequestName(): string
            {
                return 'OrderRequest';
            }

            public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface
            {
                $this->actualHandlerCalled = 'OrderRequest';

                return null;
            }
        };

        $handlerRegistry = new HandlerRegistry();
        $handlerRegistry->register($quoteMessageHandler);
        $handlerRegistry->register($orderRequestHandler);

        $builder = Builder::create();

        $processor = new Processor(
            $requestProcessor,
            $handlerRegistry,
            $builder,
        );

        $endpoint = new Endpoint(
            $serializer,
            $messageValidator,
            $processor,
        );

        $endpoint->parseAndProcessStringAsCXml($requestCxml);

        $this->assertSame($expectedHandlerCalled, $actualHandlerCalled);
    }
}
