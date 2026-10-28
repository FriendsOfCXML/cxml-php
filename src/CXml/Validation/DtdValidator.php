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

        try {
            $this->validate($xml);
        } finally {
            // reset throwing of php errors for libxml, regardless of the outcome
            libxml_use_internal_errors($internalErrors);
        }
    }

    /**
     * @throws CXmlInvalidException
     */
    private function validate(string $xml): void
    {
        $originalDomDocument = new DOMDocument();
        // disable network access for security reasons
        $originalDomDocument->loadXML($xml, LIBXML_NONET);

        $rootNode = $originalDomDocument->getElementsByTagName('cXML')->item(0)
            ?? $originalDomDocument->getElementsByTagName('Index')->item(0);

        if (null === $rootNode) {
            throw new CXmlInvalidException('Found neither "cXML" nor "Index" root node', (string)$originalDomDocument->saveXML());
        }

        // always validate against the local dtds and never against the (untrusted) doctype given in the document
        try {
            $this->validateAgainstMultipleDtd($this->pathToDtds, $rootNode, $xml);
        } catch (DOMException $domException) {
            throw new CXmlInvalidException($domException->getMessage(), (string)$originalDomDocument->saveXML(), $domException);
        }
    }

    /**
     * @throws DOMException
     */
    private function injectDtd(DOMNode $oldNode, string $dtdFilename): DOMDocument
    {
        $creator = new DOMImplementation();

        $doctype = $creator->createDocumentType($oldNode->nodeName, '', $dtdFilename);
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
    private function validateAgainstMultipleDtd(array $validateFiles, DOMNode $rootNode, string $xml): void
    {
        Assertion::allString($validateFiles);

        foreach ($validateFiles as $validateFile) {
            $dtdInjectedDomDocument = $this->injectDtd($rootNode, $validateFile);

            if ($dtdInjectedDomDocument->validate()) {
                return;
            }
        }

        throw CXmlInvalidException::fromLibXmlError(libxml_get_last_error(), $xml);
    }
}
