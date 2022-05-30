<?php

namespace CXml\Validation;

use Assert\Assertion;
use CXml\Validation\Exception\CxmlInvalidException;

class DtdValidator
{
	private string $pathToCxmlDtds;

	public function __construct(string $pathToCxmlDtds)
	{
		Assertion::directory($pathToCxmlDtds);
		Assertion::file($pathToCxmlDtds.'/cXML.dtd');
		Assertion::file($pathToCxmlDtds.'/Fulfill.dtd');

		$this->pathToCxmlDtds = $pathToCxmlDtds;
	}

	/**
	 * @throws CxmlInvalidException
	 */
	public function validateAgainstDtd(string $xml): void
	{
		if (empty($xml)) {
			throw new CxmlInvalidException('XML was empty', $xml);
		}

		// disable throwing of php errors for libxml
		$internalErrors = \libxml_use_internal_errors(true);

		$old = new \DOMDocument();
		$old->loadXML($xml);

		$validateFiles = ['cXML.dtd', 'Fulfill.dtd'];

		$this->validateAgainstMultipleDtd($validateFiles, $old);

		// reset throwing of php errors for libxml
		\libxml_use_internal_errors($internalErrors);
	}

	/**
	 * @throws CxmlInvalidException
	 * @throws \DOMException
	 */
	private function injectDtd(\DOMDocument $originalDomDocument, string $dtdFilename): \DOMDocument
	{
		$creator = new \DOMImplementation();
		$doctype = $creator->createDocumentType('cXML', '', $this->pathToCxmlDtds.'/'.$dtdFilename);
		$new = $creator->createDocument('', '', $doctype);
		$new->encoding = 'utf-8';

		$oldNode = $originalDomDocument->getElementsByTagName('cXML')->item(0);
		if (!$oldNode) {
			throw new CxmlInvalidException('Missing cXML root node', (string) $originalDomDocument->saveXML());
		}

		$newNode = $new->importNode($oldNode, true);
		$new->appendChild($newNode);

		return $new;
	}

	/**
	 * @throws CxmlInvalidException
	 * @throws \DOMException
	 */
	private function validateAgainstMultipleDtd(array $validateFiles, \DOMDocument $old): void
	{
		foreach ($validateFiles as $validateFile) {
			$dtdInjectedDomDocument = $this->injectDtd($old, $validateFile);

			if ($dtdInjectedDomDocument->validate()) {
				return;
			}
		}

		throw CxmlInvalidException::fromLibXmlError(\libxml_get_last_error(), (string) $old->saveXML());
	}
}
