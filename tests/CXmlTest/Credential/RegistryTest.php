<?php

declare(strict_types=1);

namespace CXmlTest\Credential;

use CXml\Authentication\SimpleSharedSecretAuthenticator;
use CXml\Context;
use CXml\Credential\Registry;
use CXml\Exception\CXmlAuthenticationInvalidException;
use CXml\Exception\CXmlCredentialInvalidException;
use CXml\Model\Credential;
use CXml\Model\Header;
use CXml\Model\Party;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
final class RegistryTest extends TestCase
{
    private Registry $registry;

    protected function setUp(): void
    {
        $this->registry = new Registry();
        $this->registry->registerCredential(new Credential('NetworkId', 'buyer', 'buyer-secret'));
        $this->registry->registerCredential(new Credential('DUNS', 'buyer', 'duns-secret'));
        $this->registry->registerCredential(new Credential('NetworkId', 'no-secret'));
    }

    public function testGetCredentialByDomainAndId(): void
    {
        $this->assertSame('buyer-secret', $this->registry->getCredentialByDomainAndId('NetworkId', 'buyer')->getSharedSecret());
        $this->assertSame('duns-secret', $this->registry->getCredentialByDomainAndId('DUNS', 'buyer')->getSharedSecret());
    }

    public function testGetUnknownCredential(): void
    {
        $this->expectException(CXmlCredentialInvalidException::class);
        $this->expectExceptionMessage("Could not find credentials for 'buyer@Unknown'.");

        $this->registry->getCredentialByDomainAndId('Unknown', 'buyer');
    }

    public function testValidate(): void
    {
        $this->registry->validate(new Credential('NetworkId', 'buyer'));

        $this->expectException(CXmlCredentialInvalidException::class);

        $this->registry->validate(new Credential('NetworkId', 'BUYER'));
    }

    public function testAuthenticate(): void
    {
        $this->expectNotToPerformAssertions();

        $this->registry->authenticate($this->header(new Credential('NetworkId', 'buyer', 'buyer-secret')), Context::create());
        $this->registry->authenticate($this->header(new Credential('DUNS', 'buyer', 'duns-secret')), Context::create());
        $this->registry->authenticate($this->header(new Credential('NetworkId', 'no-secret')), Context::create());
    }

    public function testAuthenticateWithSecretOfOtherDomain(): void
    {
        $this->expectException(CXmlAuthenticationInvalidException::class);

        $this->registry->authenticate($this->header(new Credential('NetworkId', 'buyer', 'duns-secret')), Context::create());
    }

    public function testAuthenticateWithoutSecret(): void
    {
        $this->expectException(CXmlAuthenticationInvalidException::class);

        $this->registry->authenticate($this->header(new Credential('NetworkId', 'buyer')), Context::create());
    }

    public function testAuthenticateUnknownSender(): void
    {
        $this->expectException(CXmlCredentialInvalidException::class);

        $this->registry->authenticate($this->header(new Credential('NetworkId', 'unknown', 'buyer-secret')), Context::create());
    }

    public function testSimpleSharedSecretAuthenticator(): void
    {
        $authenticator = new SimpleSharedSecretAuthenticator('s3cr3t');

        $authenticator->authenticate($this->header(new Credential('any', 'one', 's3cr3t')), Context::create());

        try {
            $authenticator->authenticate($this->header(new Credential('any', 'one', 'S3CR3T')), Context::create());
            $this->fail('Expected CXmlAuthenticationInvalidException');
        } catch (CXmlAuthenticationInvalidException $cXmlAuthenticationInvalidException) {
            $this->assertSame('one@any', (string)$cXmlAuthenticationInvalidException->getCredential());
            $this->assertStringNotContainsString('S3CR3T', $cXmlAuthenticationInvalidException->getMessage());
        }

        $this->expectException(CXmlAuthenticationInvalidException::class);

        $authenticator->authenticate($this->header(new Credential('any', 'one')), Context::create());
    }

    public function testCredential(): void
    {
        $credential = new Credential('NetworkId', 'buyer');

        $this->assertSame('buyer@NetworkId', (string)$credential);
        $this->assertNull($credential->getSharedSecret());
        $this->assertSame('secret', $credential->setSharedSecret('secret')->getSharedSecret());
    }

    private function header(Credential $sender): Header
    {
        return new Header(
            new Party(new Credential('NetworkId', 'buyer')),
            new Party(new Credential('NetworkId', 'supplier')),
            new Party($sender),
        );
    }
}
