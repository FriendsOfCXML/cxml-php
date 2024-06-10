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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EndpointTest extends TestCase
{

    public static function getEndpointData(): iterable
    {
        yield [
            self::loadFixture('quote_request.xml'),
            null,
        ];

        // TODO add more cases
    }

    private static function loadFixture(string $filename): ?string
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $filename);
    }

    #[DataProvider('getEndpointData')]
    public function testEndpoint(string $requestCxml, ?string $expectedResponseCxml): void
    {
        $serializer = Serializer::create();
        $messageValidator = new DtdValidator(__DIR__ . '/../../metadata/cxml/dtd/1.2.050/');

        $credentialRepository = new Registry();
        $credentialRepository->registerCredential(
            new Credential(
                'NetworkId',
                'AN00000123'
            )
        );
        $credentialRepository->registerCredential(
            new Credential(
                'NetworkId',
                'AN00000456'
            )
        );

        $authenticator = new SimpleSharedSecretAuthenticator('Secret!123');

        $requestProcessor = new HeaderProcessor(
            $credentialRepository,
            $authenticator
        );

        $handlerRegistry = new HandlerRegistry();
        $handlerRegistry->register(new class() implements HandlerInterface {
            public static function getRequestName(): string
            {
                return 'QuoteMessage';
            }

            public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface
            {
                return null;
            }
        });


        $builder = Builder::create();

        $processor = new Processor(
            $requestProcessor,
            $handlerRegistry,
            $builder
        );

        $endpoint = new Endpoint(
            $serializer,
            $messageValidator,
            $processor
        );

        $actualResponseCxml = $endpoint->parseAndProcessStringAsCXml($requestCxml);
        $this->assertEquals($expectedResponseCxml, $actualResponseCxml);
    }
}
