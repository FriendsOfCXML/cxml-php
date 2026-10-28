<?php

declare(strict_types=1);

namespace CXmlTest;

use CXml\Authentication\SimpleSharedSecretAuthenticator;
use CXml\Builder;
use CXml\Context;
use CXml\Credential\Registry;
use CXml\Endpoint;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Handler\HandlerRegistry;
use CXml\Handler\Request\StaticStartPagePunchOutSetupRequestHandler;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\Response\PunchOutSetupResponse;
use CXml\Processor\HeaderProcessor;
use CXml\Processor\Processor;
use CXml\Serializer;
use CXml\Validation\DtdValidator;
use CXml\Validation\Exception\CXmlInvalidException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Stringable;
use Throwable;

use function file_get_contents;
use function str_replace;

/**
 * @internal
 */
#[CoversNothing]
final class EndpointTest extends TestCase
{
    private const SHARED_SECRET = 'Secret!123';

    /**
     * @var array<array{level: string, message: string, context: array}>
     */
    private array $logs = [];

    private Endpoint $endpoint;

    protected function setUp(): void
    {
        $registry = new Registry();
        $registry->registerCredential(new Credential('NetworkId', 'inbound@prominate-platform.com'));
        $registry->registerCredential(new Credential('NetworkId', 'supplier@supplier.com'));

        $handlerRegistry = new HandlerRegistry();
        $handlerRegistry->register(new StaticStartPagePunchOutSetupRequestHandler('https://supplier.example.com/start'));

        $logger = new class($this->logs) extends AbstractLogger {
            public function __construct(private array &$logs)
            {
            }

            public function log($level, string|Stringable $message, array $context = []): void
            {
                $this->logs[] = ['level' => (string)$level, 'message' => (string)$message, 'context' => $context];
            }
        };

        $this->endpoint = new Endpoint(
            Serializer::create(),
            DtdValidator::fromDtdDirectory(__DIR__ . '/../metadata/cxml/dtd/1.2.063'),
            new Processor(
                new HeaderProcessor($registry, new SimpleSharedSecretAuthenticator(self::SHARED_SECRET)),
                $handlerRegistry,
                Builder::create(),
            ),
            $logger,
        );
    }

    public function testProcessPunchOutSetupRequest(): void
    {
        $context = Context::create();

        $result = $this->endpoint->parseAndProcessStringAsCXml($this->punchOutSetupRequest(), $context);

        $this->assertInstanceOf(CXml::class, $result);
        $this->assertInstanceOf(PunchOutSetupResponse::class, $result->response?->payload);
        $this->assertSame('https://supplier.example.com/start', $result->response->payload->startPage->url);
        $this->assertSame(200, $result->getStatus()?->code);
        $this->assertSame('Workchairs cXML Application', $context->getSenderUserAgent());

        $this->assertSame(['info', 'info'], array_column($this->logs, 'level'));
        $this->assertSharedSecretNotLogged();
    }

    public function testInvalidXmlIsRejected(): void
    {
        $xml = str_replace('<BuyerCookie>', '<UnknownElement/><BuyerCookie>', $this->punchOutSetupRequest());

        try {
            $this->endpoint->parseAndProcessStringAsCXml($xml);
            $this->fail('Expected CXmlInvalidException');
        } catch (CXmlInvalidException $cXmlInvalidException) {
            $this->assertStringNotContainsString(self::SHARED_SECRET, $cXmlInvalidException->getXml());
        }

        $this->assertSame(['info', 'error'], array_column($this->logs, 'level'));
        $this->assertSharedSecretNotLogged();
    }

    public function testUndeserializableXmlIsRejected(): void
    {
        // valid according to the DTD, but the timestamp cannot be parsed
        $xml = (string)preg_replace('/timestamp="[^"]+"/', 'timestamp="yesterday"', $this->punchOutSetupRequest(), 1);

        try {
            $this->endpoint->parseAndProcessStringAsCXml($xml);
            $this->fail('Expected CXmlInvalidException');
        } catch (CXmlInvalidException $cXmlInvalidException) {
            $this->assertStringStartsWith('Error while deserializing xml: Could not parse date: yesterday', $cXmlInvalidException->getMessage());
            $this->assertInstanceOf(Throwable::class, $cXmlInvalidException->getPrevious());
            $this->assertStringNotContainsString(self::SHARED_SECRET, $cXmlInvalidException->getXml());
        }

        $this->assertSame(['info', 'error'], array_column($this->logs, 'level'));
        $this->assertSharedSecretNotLogged();
    }

    public function testProcessingErrorIsLoggedAndRethrown(): void
    {
        $xml = str_replace('<SharedSecret>' . self::SHARED_SECRET . '</SharedSecret>', '<SharedSecret>wrong</SharedSecret>', $this->punchOutSetupRequest());

        try {
            $this->endpoint->parseAndProcessStringAsCXml($xml);
            $this->fail('Expected CXmlAuthenticationInvalidException');
        } catch (CXmlAuthenticationInvalidException) {
            // expected
        }

        $this->assertSame(['info', 'error'], array_column($this->logs, 'level'));
        $this->assertStringStartsWith('Error while processing valid CXml', $this->logs[1]['message']);
        $this->assertStringNotContainsString('<SharedSecret>wrong</SharedSecret>', (string)$this->logs[1]['context']['xml']);
    }

    private function assertSharedSecretNotLogged(): void
    {
        $this->assertNotEmpty($this->logs);

        foreach ($this->logs as $log) {
            $this->assertArrayHasKey('xml', $log['context']);
            $this->assertStringNotContainsString(self::SHARED_SECRET, (string)$log['context']['xml']);
            $this->assertStringContainsString('<SharedSecret>***REDACTED***</SharedSecret>', (string)$log['context']['xml']);
        }
    }

    private function punchOutSetupRequest(): string
    {
        $xml = (string)file_get_contents(__DIR__ . '/../metadata/cxml/samples/PunchOutSetupRequest.xml');

        return str_replace('<SharedSecret>s3cr3t</SharedSecret>', '<SharedSecret>' . self::SHARED_SECRET . '</SharedSecret>', $xml);
    }
}
