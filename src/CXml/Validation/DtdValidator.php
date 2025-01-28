<?php

declare(strict_types=1);

namespace CXml\Validation;

use Assert\Assertion;
use CXml\Validation\Exception\CXmlInvalidException;
use DOMDocument;
use DOMException;
use DOMImplementation;

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
        if ('' === $xml || '0' === $xml) {
            throw new CXmlInvalidException('XML was empty', $xml);
        }

        // disable throwing of php errors for libxml
        $internalErrors = libxml_use_internal_errors(true);

        $old = new DOMDocument();
        $old->loadXML($xml);

        $this->validateAgainstMultipleDtd($this->pathToDtds, $old);

        // reset throwing of php errors for libxml
        libxml_use_internal_errors($internalErrors);
    }

    /**
     * @throws CXmlInvalidException
     */
    private function injectDtd(DOMDocument $originalDomDocument, string $dtdFilename): DOMDocument
    {
        $creator = new DOMImplementation();

        try {
            $doctype = $creator->createDocumentType('cXML', '', $dtdFilename);
            $new = $creator->createDocument('', '', $doctype);
        } catch (DOMException $domException) {
            throw new CXmlInvalidException($domException->getMessage(), (string)$originalDomDocument->saveXML(), $domException);
        }

        $new->encoding = 'utf-8';

        $oldNode = $originalDomDocument->getElementsByTagName('cXML')->item(0);
        if (null === $oldNode) {
            throw new CXmlInvalidException('Missing cXML root node', (string)$originalDomDocument->saveXML());
        }

        $newNode = $new->importNode($oldNode, true);
        $new->appendChild($newNode);

        return $new;
    }

    /**
     * @throws CXmlInvalidException
     */
    private function validateAgainstMultipleDtd(array $validateFiles, DOMDocument $old): void
    {
        foreach ($validateFiles as $validateFile) {
            $dtdInjectedDomDocument = $this->injectDtd($old, $validateFile);

            if ($dtdInjectedDomDocument->validate()) {
                return;
            }
        }

        throw CXmlInvalidException::fromLibXmlError(libxml_get_last_error(), (string)$old->saveXML());
    }
}
