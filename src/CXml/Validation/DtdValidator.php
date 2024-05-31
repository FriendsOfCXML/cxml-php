<?php

declare(strict_types=1);

namespace CXml\Validation;

use Assert\Assertion;
use CXml\Validation\Exception\CXmlInvalidException;

readonly class DtdValidator
{
    public function __construct(private string $pathToCxmlDtds)
    {
        Assertion::directory($pathToCxmlDtds);
        Assertion::file($pathToCxmlDtds . '/cXML.dtd');
        Assertion::file($pathToCxmlDtds . '/Fulfill.dtd');
        Assertion::file($pathToCxmlDtds . '/Quote.dtd');
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
        $internalErrors = \libxml_use_internal_errors(true);

        $old = new \DOMDocument();
        $old->loadXML($xml);

        $validateFiles = ['cXML.dtd', 'Fulfill.dtd', 'Quote.dtd'];

        $this->validateAgainstMultipleDtd($validateFiles, $old);

        // reset throwing of php errors for libxml
        \libxml_use_internal_errors($internalErrors);
    }

    /**
     * @throws CXmlInvalidException
     */
    private function injectDtd(\DOMDocument $originalDomDocument, string $dtdFilename): \DOMDocument
    {
        $creator = new \DOMImplementation();

        try {
            $doctype = $creator->createDocumentType('cXML', '', $this->pathToCxmlDtds . '/' . $dtdFilename);
            $new = $creator->createDocument('', '', $doctype);
        } catch (\DOMException $domException) {
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
    private function validateAgainstMultipleDtd(array $validateFiles, \DOMDocument $old): void
    {
        foreach ($validateFiles as $validateFile) {
            $dtdInjectedDomDocument = $this->injectDtd($old, $validateFile);

            if ($dtdInjectedDomDocument->validate()) {
                return;
            }
        }

        throw CXmlInvalidException::fromLibXmlError(\libxml_get_last_error(), (string)$old->saveXML());
    }
}
