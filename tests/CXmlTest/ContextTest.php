<?php

declare(strict_types=1);

namespace CXmlTest;

use CXml\Context;
use CXml\Model\Credential;
use CXml\Model\CXml;
use CXml\Model\Header;
use CXml\Model\Message\Message;
use CXml\Model\Message\PunchOutOrderMessage;
use CXml\Model\Message\PunchOutOrderMessageHeader;
use CXml\Model\MoneyWrapper;
use CXml\Model\Party;
use CXml\Model\PayloadIdentity;
use CXml\Model\Response\Response;
use CXml\Model\Status;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class ContextTest extends TestCase
{
    public function testOptions(): void
    {
        $context = Context::create(['foo' => 'bar']);

        $this->assertSame('bar', $context->getOption('foo'));
        $this->assertNull($context->getOption('unknown'));
        $this->assertSame(['foo' => 'bar'], $context->getOptions());
    }

    public function testDryRun(): void
    {
        $context = Context::create();
        $this->assertFalse($context->isDryRun());

        $context->setDryRun(true);
        $this->assertTrue($context->isDryRun());
        $this->assertSame(['dryrun' => true], $context->getOptions());

        $this->assertTrue(Context::create(['dryrun' => 1])->isDryRun());
    }

    public function testWithoutCXml(): void
    {
        $context = Context::create();

        $this->assertNull($context->getCXml());
        $this->assertNull($context->getPayloadId());
        $this->assertNull($context->getSenderUserAgent());
    }

    public function testWithResponseCXml(): void
    {
        $cxml = CXml::forResponse(new PayloadIdentity('payload-id'), new Response(new Status()));
        $context = Context::create()->setCXml($cxml);

        $this->assertSame($cxml, $context->getCXml());
        $this->assertSame('payload-id', $context->getPayloadId());
        $this->assertNull($context->getSenderUserAgent(), 'responses have no header');
    }

    public function testSenderUserAgent(): void
    {
        $credential = new Credential('NetworkId', 'id');
        $cxml = CXml::forMessage(
            new PayloadIdentity('payload-id'),
            new Message(PunchOutOrderMessage::create('cookie', new PunchOutOrderMessageHeader(new MoneyWrapper('EUR', 1)))),
            new Header(new Party($credential), new Party($credential), new Party($credential, 'agent/1.0')),
        );

        $this->assertSame('agent/1.0', Context::create()->setCXml($cxml)->getSenderUserAgent());
    }
}
