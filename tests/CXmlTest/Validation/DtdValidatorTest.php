<?php

declare(strict_types=1);

namespace CXmlTest\Validation;

use CXml\Validation\DtdValidator;
use CXml\Validation\Exception\CXmlInvalidException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function libxml_use_internal_errors;
use function str_replace;

/**
 * @internal
 */
#[CoversNothing]
final class DtdValidatorTest extends TestCase
{
    private const DTD_DIR = __DIR__ . '/../../metadata/cxml/dtd/1.2.063';

    private DtdValidator $dtdValidator;

    protected function setUp(): void
    {
        $this->dtdValidator = DtdValidator::fromDtdDirectory(self::DTD_DIR);
    }

    public function testFromDtdDirectoryRequiresExistingDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DtdValidator::fromDtdDirectory(__DIR__ . '/does-not-exist');
    }

    public function testFromDtdDirectoryRequiresDtdFiles(): void
    {
        $this->expectException(InvalidArgumentException::class);

        DtdValidator::fromDtdDirectory(__DIR__);
    }

    public function testConstructorRequiresDtds(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new DtdValidator([]);
    }

    public function testValidateEmptyXml(): void
    {
        $this->expectException(CXmlInvalidException::class);
        $this->expectExceptionMessage('XML was empty');

        $this->dtdValidator->validateAgainstDtd('');
    }

    public function testValidateMalformedXml(): void
    {
        $this->expectException(CXmlInvalidException::class);

        $this->dtdValidator->validateAgainstDtd('<cXML payloadID="x"');
    }

    #[DataProvider('provideValidSamples')]
    public function testValidSamples(string $sample): void
    {
        $this->expectNotToPerformAssertions();

        $this->dtdValidator->validateAgainstDtd((string)file_get_contents(__DIR__ . '/../../metadata/cxml/samples/' . $sample));
    }

    public static function provideValidSamples(): iterable
    {
        yield ['simple-profile-request.xml'];
        yield ['OrderRequest.xml'];
        yield ['Index.xml'];
        yield ['InvoiceDetailRequest.xml'];
        yield ['InvoiceDetailRequestWithComments.xml'];
    }

    public function testInvalidIndex(): void
    {
        $this->expectException(CXmlInvalidException::class);

        $this->dtdValidator->validateAgainstDtd('<?xml version="1.0" encoding="UTF-8"?><Index loadmode="Full"></Index>');
    }

    public function testDoctypeOfDocumentIsIgnored(): void
    {
        $this->expectNotToPerformAssertions();

        // the doctype of an incoming document must never be loaded (i.e. SSRF), the local dtds must be used instead
        $doctype = '<!DOCTYPE %s SYSTEM "http://127.0.0.1:1/must-not-be-loaded.dtd">';

        $index = (string)file_get_contents(__DIR__ . '/../../metadata/cxml/samples/Index.xml');
        $index = (string)preg_replace('/<!DOCTYPE[^>]+>/', sprintf($doctype, 'Index'), $index);

        $this->dtdValidator->validateAgainstDtd($index);

        $profileRequest = (string)file_get_contents(__DIR__ . '/../../metadata/cxml/samples/simple-profile-request.xml');
        $profileRequest = str_replace('?>', '?>' . sprintf($doctype, 'cXML'), $profileRequest);

        $this->dtdValidator->validateAgainstDtd($profileRequest);
    }

    public function testFallsBackToNextDtd(): void
    {
        $restrictiveDtd = (string)tempnam(sys_get_temp_dir(), 'cxml-dtd');
        file_put_contents($restrictiveDtd, '<!ELEMENT cXML EMPTY>');

        try {
            $xml = (string)file_get_contents(__DIR__ . '/../../metadata/cxml/samples/simple-profile-request.xml');

            try {
                (new DtdValidator([$restrictiveDtd]))->validateAgainstDtd($xml);
                $this->fail('Expected CXmlInvalidException');
            } catch (CXmlInvalidException) {
                // expected, the restrictive dtd does not allow any children
            }

            // first dtd does not match, second one does
            (new DtdValidator([$restrictiveDtd, self::DTD_DIR . '/cXML.dtd']))->validateAgainstDtd($xml);
            $this->addToAssertionCount(1);
        } finally {
            unlink($restrictiveDtd);
        }
    }

    public function testInvalidDocumentReportsLibXmlError(): void
    {
        $xml = (string)file_get_contents(__DIR__ . '/../../metadata/cxml/samples/simple-profile-request.xml');
        $xml = str_replace('<ProfileRequest />', '<UnknownRequest />', $xml);

        try {
            $this->dtdValidator->validateAgainstDtd($xml);
            $this->fail('Expected CXmlInvalidException');
        } catch (CXmlInvalidException $cXmlInvalidException) {
            $this->assertStringContainsString('UnknownRequest', $cXmlInvalidException->getMessage());
            $this->assertStringContainsString('at line', $cXmlInvalidException->getMessage());
            $this->assertStringNotContainsString('abracadabra', $cXmlInvalidException->getXml());
        }
    }

    public function testLibXmlErrorHandlingIsRestored(): void
    {
        $previous = libxml_use_internal_errors(false);

        try {
            $this->dtdValidator->validateAgainstDtd((string)file_get_contents(__DIR__ . '/../../metadata/cxml/samples/Index.xml'));
            $this->assertFalse(libxml_use_internal_errors(false), 'after valid Index');

            foreach (['<cXML></cXML>', '<Index></Index>', '<other/>'] as $invalidXml) {
                try {
                    $this->dtdValidator->validateAgainstDtd($invalidXml);
                    $this->fail('Expected CXmlInvalidException');
                } catch (CXmlInvalidException) {
                    $this->assertFalse(libxml_use_internal_errors(false), 'after invalid ' . $invalidXml);
                }
            }
        } finally {
            libxml_use_internal_errors($previous);
        }
    }
}
