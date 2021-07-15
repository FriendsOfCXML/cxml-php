<?php

namespace Mathielen\CXml\Document;

use Mathielen\CXml\Model\CXml;
use Mathielen\CXml\Model\DocumentReference;

interface DocumentRegistryInterface
{

	public function register(CXml $cxml): void;
	public function getByDocumentReference(DocumentReference $documentReference): ?CXml;

}