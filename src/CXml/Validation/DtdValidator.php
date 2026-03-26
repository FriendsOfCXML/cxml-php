<?php

declare(strict_types=1);

namespace CXml\Validation;

use Assert\Assertion;
use CXml\Validation\Exception\CXmlInvalidException;
use DOMDocument;
use DOMException;
use DOMImplementation;
use DOMNode;

use function libxml_get_last_error;
use function libxml_use_internal_errors;

readonly class DtdValidator
{
    public function __construct(
        private array $pathToDtds,
    ) {
        Assertion::notEmpty($pathToDtds);
    }

    public static function fromDtdDirectory(string $directory): self
    {
        Assertion::directory($directory);

        $pathToDtds = glob($directory . '/*.dtd');
        Assertion::notEmpty($pathToDtds);

        return new self($pathToDtds);
    }

    /**
     * @throws CXmlInvalidException
     */
    public function validateAgainstDtd(string $xml): void
    {
        if ('' === $xml) {
            throw new CXmlInvalidException('XML was empty', $xml);
        }

        // disable throwing of php errors for libxml
        $internalErrors = libxml_use_internal_errors(true);

        $originalDomDocument = new DOMDocument();
        // disable network access for security reasons
        $originalDomDocument->loadXML($xml, LIBXML_NONET);

        $cxmlNode = $originalDomDocument->getElementsByTagName('cXML')->item(0);
        if (null !== $cxmlNode) {
            try {
                $this->validateAgainstMultipleDtd($this->pathToDtds, $cxmlNode, $xml);

                // reset throwing of php errors for libxml
                libxml_use_internal_errors($internalErrors);

                return;
            } catch (DOMException $domException) {
                // reset throwing of php errors for libxml
                libxml_use_internal_errors($internalErrors);

                throw new CXmlInvalidException($domException->getMessage(), (string)$originalDomDocument->saveXML(), $domException);
            }
        }

        $indexNode = $originalDomDocument->getElementsByTagName('Index')->item(0);
        if (null !== $indexNode) {
            if ($originalDomDocument->validate()) {
                return;
            }

            $e = CXmlInvalidException::fromLibXmlError(libxml_get_last_error(), $xml);

            // reset throwing of php errors for libxml
            libxml_use_internal_errors($internalErrors);

            throw $e;
        }

        throw new CXmlInvalidException('Found neither "cXML" nor "Index" root node', (string)$originalDomDocument->saveXML());
    }

    /**
     * @throws DOMException
     */
    private function injectDtd(DOMNode $oldNode, string $dtdFilename): DOMDocument
    {
        $creator = new DOMImplementation();

        $doctype = $creator->createDocumentType('cXML', '', $dtdFilename);
        $new = $creator->createDocument('', '', $doctype);
        $new->encoding = 'utf-8';

        $newNode = $new->importNode($oldNode, true);
        $new->appendChild($newNode);

        return $new;
    }

    /**
     * @throws CXmlInvalidException
     * @throws DOMException
     */
    private function validateAgainstMultipleDtd(array $validateFiles, DOMNode $indexNode, string $xml): void
    {
        Assertion::allString($validateFiles);

        foreach ($validateFiles as $validateFile) {
            $dtdInjectedDomDocument = $this->injectDtd($indexNode, $validateFile);

            if ($dtdInjectedDomDocument->validate()) {
                return;
            }
        }

        throw CXmlInvalidException::fromLibXmlError(libxml_get_last_error(), $xml);
    }
}
