<?php

declare(strict_types=1);

namespace CXmlTest\Validation;

use CXml\Exception\CXmlNotAcceptableException;
use CXml\Validation\Exception\CXmlInvalidException;
use LibXMLError;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @internal
 */
#[CoversNothing]
final class CXmlInvalidExceptionTest extends TestCase
{
    private const XML = "<Sender><Credential domain=\"NetworkId\"><Identity>me</Identity><SharedSecret>top\nsecret</SharedSecret></Credential></Sender>";

    public function testSharedSecretIsRedacted(): void
    {
        $previous = new RuntimeException('previous');
        $exception = new CXmlInvalidException('invalid', self::XML, $previous);

        $this->assertInstanceOf(CXmlNotAcceptableException::class, $exception);
        $this->assertSame('invalid', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame('<Sender><Credential domain="NetworkId"><Identity>me</Identity><SharedSecret>***REDACTED***</SharedSecret></Credential></Sender>', $exception->getXml());
    }

    public function testFromLibXmlError(): void
    {
        $libXmlError = new LibXMLError();
        $libXmlError->message = "Element cXML content does not follow the DTD\n";
        $libXmlError->line = 3;
        $libXmlError->column = 7;
        $libXmlError->code = 518;

        $exception = CXmlInvalidException::fromLibXmlError($libXmlError, self::XML);

        $this->assertSame('Element cXML content does not follow the DTD at line 3, column 7. Code 518.', $exception->getMessage());
        $this->assertStringNotContainsString('secret', $exception->getXml());
    }

    public function testFromMissingLibXmlError(): void
    {
        $exception = CXmlInvalidException::fromLibXmlError(false, self::XML);

        $this->assertSame('No LibXMLError was given.', $exception->getMessage());
        $this->assertStringNotContainsString('secret', $exception->getXml());
    }
}
