<?php

declare(strict_types=1);

namespace CXmlTest\Processor;

use CXml\Builder;
use CXml\Context;
use CXml\Credential\Registry;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Exception\CXmlException;
use CXml\Handler\Exception\CXmlHandlerNotFoundException;
use CXml\Handler\HandlerInterface;
use CXml\Handler\HandlerRegistry;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\PayloadIdentity;
use CXml\Model\PayloadInterface;
use CXml\Model\Request\ProfileRequest;
use CXml\Model\Response\ProfileResponse;
use CXml\Model\Response\PunchOutSetupResponse;
use CXml\Model\Response\Response;
use CXml\Model\Response\ResponsePayloadInterface;
use CXml\Model\Status;
use CXml\Model\Url;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Processor\Event\CXmlProcessEvent;
use CXml\Processor\Exception\CXmlProcessException;
use CXml\Processor\HeaderProcessor;
use CXml\Processor\Processor;
use CXml\Serializer;
use DateTime;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Throwable;

/**
 * @internal
 */
#[CoversNothing]
final class ProcessorTest extends TestCase implements PayloadIdentityFactoryInterface
{
    private const SHARED_SECRET = 's3cr3t';

    private HandlerRegistry $handlerRegistry;

    /**
     * @var object[]
     */
    private array $dispatchedEvents = [];

    private Processor $processor;

    protected function setUp(): void
    {
        $registry = new Registry();
        $registry->registerCredential(new Credential('NetworkId', 'buyer', self::SHARED_SECRET));
        $registry->registerCredential(new Credential('NetworkId', 'supplier'));

        $this->handlerRegistry = new HandlerRegistry();

        $eventDispatcher = new class($this->dispatchedEvents) implements EventDispatcherInterface {
            public function __construct(private array &$events)
            {
            }

            public function dispatch(object $event): object
            {
                $this->events[] = $event;

                return $event;
            }
        };

        $this->processor = new Processor(
            new HeaderProcessor($registry, $registry),
            $this->handlerRegistry,
            Builder::create('supplier-agent', null, $this),
            $eventDispatcher,
        );
    }

    public function testRequestWithoutResponsePayloadResultsInOkStatus(): void
    {
        $handler = $this->registerHandler('ProfileRequest');
        $context = Context::create(['foo' => 'bar']);
        $cxml = $this->buildRequest(new ProfileRequest());

        $result = $this->processor->process($cxml, $context);

        $this->assertInstanceOf(CXml::class, $result);
        $this->assertInstanceOf(Response::class, $result->response);
        $this->assertNull($result->response->payload);
        $this->assertSame(200, $result->getStatus()?->code);

        $this->assertCount(1, $handler->handled);
        $this->assertInstanceOf(ProfileRequest::class, $handler->handled[0][0]);
        $this->assertSame($context, $handler->handled[0][1]);
        $this->assertSame($cxml, $context->getCXml());
        $this->assertSame('bar', $context->getOption('foo'));

        $this->assertCount(1, $this->dispatchedEvents);
        $this->assertInstanceOf(CXmlProcessEvent::class, $this->dispatchedEvents[0]);
        $this->assertSame($cxml, $this->dispatchedEvents[0]->getCxml());
        $this->assertSame($context, $this->dispatchedEvents[0]->getContext());
    }

    public function testRequestWithResponsePayload(): void
    {
        $responsePayload = new PunchOutSetupResponse(new Url('https://supplier.example.com/start'));
        $this->registerHandler('ProfileRequest', $responsePayload);

        $result = $this->processor->process($this->buildRequest(new ProfileRequest()));

        $this->assertSame($responsePayload, $result?->response?->payload);
        $this->assertSame(200, $result?->getStatus()?->code);
    }

    public function testRequestCreatesContextIfNoneGiven(): void
    {
        $handler = $this->registerHandler('ProfileRequest');
        $cxml = $this->buildRequest(new ProfileRequest());

        $this->processor->process($cxml);

        $this->assertSame($cxml, $handler->handled[0][1]->getCXml());
    }

    public function testRequestWithUnknownSenderIsRejected(): void
    {
        $handler = $this->registerHandler('ProfileRequest');

        try {
            $this->processor->process($this->buildRequest(new ProfileRequest(), new Credential('NetworkId', 'unknown', self::SHARED_SECRET)));
            $this->fail('Expected CXmlCredentialInvalidException');
        } catch (CXmlCredentialInvalidException $cXmlCredentialInvalidException) {
            $this->assertNotInstanceOf(CXmlAuthenticationInvalidException::class, $cXmlCredentialInvalidException);
        }

        $this->assertSame([], $handler->handled);
    }

    public function testRequestWithWrongSharedSecretIsRejected(): void
    {
        $handler = $this->registerHandler('ProfileRequest');

        try {
            $this->processor->process($this->buildRequest(new ProfileRequest(), new Credential('NetworkId', 'buyer', 'wrong')));
            $this->fail('Expected CXmlAuthenticationInvalidException');
        } catch (CXmlAuthenticationInvalidException $cXmlAuthenticationInvalidException) {
            $this->assertSame('buyer', $cXmlAuthenticationInvalidException->getCredential()?->identity);
        }

        $this->assertSame([], $handler->handled);
    }

    public function testRequestWithoutHandler(): void
    {
        $this->expectException(CXmlHandlerNotFoundException::class);

        $this->processor->process($this->buildRequest(new ProfileRequest()));
    }

    public function testRequestWithoutHeader(): void
    {
        $this->registerHandler('ProfileRequest');

        $this->expectException(CXmlException::class);
        $this->expectExceptionMessage('Header is mandatory for request');

        $this->processor->process($this->deserialize('<Request><ProfileRequest/></Request>'));
    }

    public function testMessageIsHandled(): void
    {
        $handler = $this->registerHandler('PunchOutOrderMessage');

        $result = $this->processor->process($this->deserialize($this->header() . $this->punchOutOrderMessage()));

        $this->assertNull($result, 'messages do not have a response');
        $this->assertCount(1, $handler->handled);
    }

    public function testMessageWithoutHeader(): void
    {
        $this->registerHandler('PunchOutOrderMessage');

        $this->expectException(CXmlException::class);
        $this->expectExceptionMessage('Header is mandatory for message');

        $this->processor->process($this->deserialize($this->punchOutOrderMessage()));
    }

    public function testMessageWithInvalidCredentials(): void
    {
        $handler = $this->registerHandler('PunchOutOrderMessage');

        try {
            $this->processor->process($this->deserialize($this->header('wrong') . $this->punchOutOrderMessage()));
            $this->fail('Expected CXmlAuthenticationInvalidException');
        } catch (CXmlAuthenticationInvalidException) {
            $this->assertSame([], $handler->handled);
        }
    }

    public function testMessageHandlerErrorsAreWrapped(): void
    {
        $exception = new RuntimeException('handler failed');
        $this->registerHandler('PunchOutOrderMessage', null, $exception);

        try {
            $this->processor->process($this->deserialize($this->header() . $this->punchOutOrderMessage()));
            $this->fail('Expected CXmlProcessException');
        } catch (CXmlProcessException $cXmlProcessException) {
            $this->assertSame($exception, $cXmlProcessException->getPrevious());
            $this->assertSame('Error while processing cXML message: handler failed', $cXmlProcessException->getMessage());
        }
    }

    public function testMessageHandlerCXmlExceptionsAreNotWrapped(): void
    {
        $exception = new CXmlException('handler failed');
        $this->registerHandler('PunchOutOrderMessage', null, $exception);

        try {
            $this->processor->process($this->deserialize($this->header() . $this->punchOutOrderMessage()));
            $this->fail('Expected CXmlException');
        } catch (CXmlException $cXmlException) {
            $this->assertSame($exception, $cXmlException);
        }
    }

    public function testResponseWithPayloadIsHandled(): void
    {
        $handler = $this->registerHandler('ProfileResponse');

        $result = $this->processor->process($this->deserialize('<Response><Status code="200" text="OK"/><ProfileResponse effectiveDate="2025-01-01T00:00:00+00:00"/></Response>'));

        $this->assertNull($result);
        $this->assertCount(1, $handler->handled);
        $this->assertInstanceOf(ProfileResponse::class, $handler->handled[0][0]);
    }

    public function testResponseWithoutPayloadIsIgnored(): void
    {
        $result = $this->processor->process($this->deserialize('<Response><Status code="200" text="OK"/></Response>'));

        $this->assertNull($result);
    }

    public function testResponseHandlerErrorsAreWrapped(): void
    {
        $this->registerHandler('ProfileResponse', null, new RuntimeException('handler failed'));

        $this->expectException(CXmlProcessException::class);

        $this->processor->process($this->deserialize('<Response><Status code="200" text="OK"/><ProfileResponse effectiveDate="2025-01-01T00:00:00+00:00"/></Response>'));
    }

    public function testEmptyCXml(): void
    {
        $this->expectException(CXmlException::class);
        $this->expectExceptionMessage('Either request, response or message must be given');

        $this->processor->process($this->deserialize(''));
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity('payload-id', new DateTime('2025-01-01T00:00:00+00:00'));
    }

    private function buildRequest(ProfileRequest $payload, ?Credential $sender = null): CXml
    {
        return Builder::create('buyer-agent', null, $this)
            ->from(new Credential('NetworkId', 'buyer'))
            ->to(new Credential('NetworkId', 'supplier'))
            ->sender($sender ?? new Credential('NetworkId', 'buyer', self::SHARED_SECRET))
            ->payload($payload)
            ->build();
    }

    private function deserialize(string $innerXml): CXml
    {
        return Serializer::create()->deserialize(
            '<?xml version="1.0" encoding="UTF-8"?><cXML payloadID="x" timestamp="2025-01-01T00:00:00+00:00">' . $innerXml . '</cXML>',
        );
    }

    private function header(string $sharedSecret = self::SHARED_SECRET): string
    {
        return <<<XML
            <Header>
                <From><Credential domain="NetworkId"><Identity>supplier</Identity></Credential></From>
                <To><Credential domain="NetworkId"><Identity>buyer</Identity></Credential></To>
                <Sender><Credential domain="NetworkId"><Identity>buyer</Identity><SharedSecret>{$sharedSecret}</SharedSecret></Credential><UserAgent>agent</UserAgent></Sender>
            </Header>
            XML;
    }

    private function punchOutOrderMessage(): string
    {
        return <<<'XML'
            <Message>
                <PunchOutOrderMessage>
                    <BuyerCookie>cookie</BuyerCookie>
                    <PunchOutOrderMessageHeader operationAllowed="create">
                        <Total><Money currency="EUR">1.00</Money></Total>
                    </PunchOutOrderMessageHeader>
                </PunchOutOrderMessage>
            </Message>
            XML;
    }

    /**
     * @return HandlerInterface&object{handled: array<array{PayloadInterface, Context}>}
     */
    private function registerHandler(string $handlerId, ?ResponsePayloadInterface $response = null, ?Throwable $exception = null): HandlerInterface
    {
        $handler = new class($response, $exception) implements HandlerInterface {
            /**
             * @var array<array{PayloadInterface, Context}>
             */
            public array $handled = [];

            public function __construct(private readonly ?ResponsePayloadInterface $response, private readonly ?Throwable $exception)
            {
            }

            public function handle(PayloadInterface $payload, Context $context): ?ResponsePayloadInterface
            {
                $this->handled[] = [$payload, $context];

                if ($this->exception instanceof Throwable) {
                    throw $this->exception;
                }

                return $this->response;
            }

            public static function getRequestName(): string
            {
                return 'Recording';
            }
        };

        $this->handlerRegistry->register($handler, $handlerId);

        return $handler;
    }

    public function testStatusOfResponseIsKept(): void
    {
        $status = $this->deserialize('<Response><Status code="406" text="Not Acceptable">invalid</Status></Response>')->getStatus();

        $this->assertInstanceOf(Status::class, $status);
        $this->assertSame(406, $status->code);
        $this->assertSame('invalid', $status->message);
    }
}
