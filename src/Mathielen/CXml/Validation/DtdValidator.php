<?php

namespace Mathielen\CXml\Validation;

use Assert\Assertion;
use Mathielen\CXml\Validation\Exception\InvalidCxmlException;

class DtdValidator
{
    private string $pathToCxmlDtd;

    public function __construct(string $pathToCxmlDtd)
    {
        Assertion::file($pathToCxmlDtd);
        //TODO test if its the cXML.dtd

        $this->pathToCxmlDtd = $pathToCxmlDtd;
    }

    /**
     * @throws InvalidCxmlException
     */
    public function validateAgainstDtd(string $xml): void
    {
        //disable throwing of php errors for libxml
        $internalErrors = libxml_use_internal_errors(true);

        $old = new \DOMDocument();
        $old->loadXML($xml);

        $dtdinjectedDomDocument = $this->injectDtd($old, );
        if (!$dtdinjectedDomDocument->validate()) {
            throw InvalidCxmlException::fromLibXmlError(libxml_get_last_error(), $xml);
        }

        //reset throwing of php errors for libxml
        libxml_use_internal_errors($internalErrors);
    }

    /**
     * @throws InvalidCxmlException
     */
    private function injectDtd(\DOMDocument $originalDomDocument): \DOMDocument
    {
        $creator = new \DOMImplementation();
        $doctype = $creator->createDocumentType('cXML', null, $this->pathToCxmlDtd);
        $new = $creator->createDocument(null, null, $doctype);
        $new->encoding = "utf-8";

        $oldNode = $originalDomDocument->getElementsByTagName('cXML')->item(0);
        if (!$oldNode) {
            throw new InvalidCxmlException("Missing cXML root node", $originalDomDocument->saveXML());
        }

        $newNode = $new->importNode($oldNode, true);
        $new->appendChild($newNode);

        return $new;
    }
}
