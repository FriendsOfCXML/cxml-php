<?php

declare(strict_types=1);

namespace CXmlTest;

use CXml\Builder;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlConflictException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Exception\CXmlException;
use CXml\Exception\CXmlExpectationFailedException;
use CXml\Exception\CXmlNotAcceptableException;
use CXml\Exception\CXmlNotImplementedException;
use CXml\Exception\CXmlPreconditionFailedException;
use CXml\Handler\Exception\CXmlHandlerNotFoundException;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\Exception\CXmlModelNotFoundException;
use CXml\Model\Header;
use CXml\Model\Message\Message;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\PayloadIdentity;
use CXml\Model\Request\ProfileRequest;
use CXml\Model\Request\Request;
use CXml\Model\Response\ProfileResponse;
use CXml\Model\Response\Response;
use CXml\Model\Status;
use CXml\Payload\PayloadIdentityFactoryInterface;
use CXml\Processor\Exception\CXmlProcessException;
use CXml\Validation\Exception\CXmlInvalidException;
use DateTime;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
#[CoversNothing]
final class BuilderTest extends TestCase implements PayloadIdentityFactoryInterface
{
    public function testBuildRequest(): void
    {
        $cxml = $this->createBuilderWithParties()
            ->payload(new ProfileRequest())
            ->build(CXml::DEPLOYMENT_TEST);

        $this->assertSame('payload-id', $cxml->payloadId);
        $this->assertSame('de-DE', $cxml->lang);
        $this->assertSame('http://xml.cxml.org/schemas/cXML/1.2.063/cXML.dtd', $cxml->dtdUri);
        $this->assertInstanceOf(Request::class, $cxml->request);
        $this->assertInstanceOf(ProfileRequest::class, $cxml->request->payload);
        $this->assertSame(CXml::DEPLOYMENT_TEST, $cxml->request->deploymentMode);
        $this->assertNull($cxml->response);
        $this->assertNull($cxml->message);
        $this->assertNull($cxml->getStatus());

        $this->assertInstanceOf(Header::class, $cxml->header);
        $this->assertSame('from@domain', (string)$cxml->header->from->credential);
        $this->assertSame('to@domain', (string)$cxml->header->to->credential);
        $this->assertSame('sender@domain', (string)$cxml->header->sender->credential);
        $this->assertSame('test-agent', $cxml->header->sender->userAgent);
        $this->assertNull($cxml->header->from->userAgent);
    }

    public function testBuildMessage(): void
    {
        $payload = PunchOutOrderMessage::create('cookie', new PunchOutOrderMessageHeader(new MoneyWrapper('EUR', 100)));
        $status = new Status(201, 'Accepted');

        $cxml = $this->createBuilderWithParties()
            ->payload($payload)
            ->status($status)
            ->build(CXml::DEPLOYMENT_PROD);

        $this->assertInstanceOf(Message::class, $cxml->message);
        $this->assertSame($payload, $cxml->message->payload);
        $this->assertSame(CXml::DEPLOYMENT_PROD, $cxml->message->deploymentMode);
        $this->assertNull($cxml->message->inReplyTo);
        $this->assertNull($cxml->message->id);
        $this->assertSame($status, $cxml->getStatus());
        $this->assertInstanceOf(Header::class, $cxml->header);
    }

    public function testBuildResponseHasImplicitOkStatus(): void
    {
        $payload = new ProfileResponse();

        $cxml = Builder::create('test-agent', null, $this)
            ->payload($payload)
            ->build();

        $this->assertInstanceOf(Response::class, $cxml->response);
        $this->assertSame($payload, $cxml->response->payload);
        $this->assertSame(200, $cxml->response->status->code);
        $this->assertSame('OK', $cxml->response->status->text);
        $this->assertNull($cxml->header, 'responses do not have a header');
    }

    public function testBuildStatusOnlyResponse(): void
    {
        $cxml = Builder::create('test-agent', null, $this)
            ->status(new Status(204, 'No Content'))
            ->build();

        $this->assertInstanceOf(Response::class, $cxml->response);
        $this->assertNull($cxml->response->payload);
        $this->assertSame(204, $cxml->getStatus()?->code);
    }

    public function testCustomDtdUriAndUserAgent(): void
    {
        $cxml = $this->createBuilderWithParties()
            ->dtdUri('http://xml.cxml.org/schemas/cXML/1.2.050/cXML.dtd')
            ->setSenderUserAgent('other-agent')
            ->payload(new ProfileRequest())
            ->build();

        $this->assertSame('http://xml.cxml.org/schemas/cXML/1.2.050/cXML.dtd', $cxml->dtdUri);
        $this->assertSame('other-agent', $cxml->header?->sender->userAgent);
    }

    public function testBuildWithoutPayloadOrStatus(): void
    {
        $this->expectException(CXmlException::class);

        Builder::create('test-agent', null, $this)->build();
    }

    public function testInvalidDeploymentMode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createBuilderWithParties()
            ->payload(new ProfileRequest())
            ->build('staging');
    }

    #[DataProvider('provideMissingParties')]
    public function testRequestRequiresAllParties(string $missingParty): void
    {
        $builder = Builder::create('test-agent', null, $this)->payload(new ProfileRequest());

        foreach (['from', 'to', 'sender'] as $party) {
            if ($party !== $missingParty) {
                $builder->{$party}(new Credential('domain', $party));
            }
        }

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf("No '%s' has been set.", $missingParty));

        $builder->build();
    }

    public static function provideMissingParties(): iterable
    {
        yield 'from' => ['from'];
        yield 'to' => ['to'];
        yield 'sender' => ['sender'];
    }

    #[DataProvider('provideExceptions')]
    public function testBuildResponseForException(CXmlException $exception, int $expectedCode, string $expectedText): void
    {
        $cxml = Builder::create('test-agent', null, $this)->buildResponseForException($exception);

        $status = $cxml->getStatus();
        $this->assertInstanceOf(Status::class, $status);
        $this->assertSame($expectedCode, $status->code);
        $this->assertSame($expectedText, $status->text);
        $this->assertSame($exception->getMessage(), $status->message);
    }

    public static function provideExceptions(): iterable
    {
        $credential = new Credential('domain', 'identity');

        yield 'authentication' => [new CXmlAuthenticationInvalidException($credential), 401, 'Unauthorized'];
        yield 'not acceptable' => [new CXmlNotAcceptableException('message'), 406, 'Not Acceptable'];
        yield 'invalid xml' => [new CXmlInvalidException('message', '<xml/>'), 406, 'Not Acceptable'];
        yield 'conflict' => [new CXmlConflictException('message'), 409, 'Conflict'];
        yield 'precondition failed' => [new CXmlPreconditionFailedException('message'), 412, 'Precondition Failed'];
        yield 'expectation failed' => [new CXmlExpectationFailedException('message'), 417, 'Expectation Failed'];
        yield 'model not found' => [new CXmlModelNotFoundException('Foo'), 450, 'Not Implemented'];
        yield 'credential invalid' => [new CXmlCredentialInvalidException('message', $credential), 417, 'Expectation Failed'];
        yield 'not implemented' => [new CXmlNotImplementedException('message'), 450, 'Not Implemented'];
        yield 'generic' => [new CXmlException('message'), 500, 'Internal Server Error'];
    }

    public function testBuildResponseForSubclassedException(): void
    {
        $cxml = Builder::create('test-agent', null, $this)->buildResponseForException(new CXmlHandlerNotFoundException('OrderRequest'));

        $this->assertSame(450, $cxml->getStatus()?->code);
        $this->assertSame('Not Implemented', $cxml->getStatus()?->text);
        $this->assertSame('Handler for OrderRequest not found. Register first.', $cxml->getStatus()?->message);
    }

    public function testBuildResponseForProcessException(): void
    {
        $cxml = Builder::create('test-agent', null, $this)->buildResponseForException(new CXmlProcessException(new RuntimeException('boom')));

        $this->assertSame(500, $cxml->getStatus()?->code);
        $this->assertSame('Error while processing cXML message: boom', $cxml->getStatus()?->message);
    }

    public function newPayloadIdentity(): PayloadIdentity
    {
        return new PayloadIdentity('payload-id', new DateTime('2025-01-01T00:00:00+00:00'));
    }

    private function createBuilderWithParties(): Builder
    {
        return Builder::create('test-agent', 'de-DE', $this)
            ->from(new Credential('domain', 'from'))
            ->to(new Credential('domain', 'to'))
            ->sender(new Credential('domain', 'sender', 'secret'));
    }
}
