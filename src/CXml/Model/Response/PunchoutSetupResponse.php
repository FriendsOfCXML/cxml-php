<?php

namespace CXml\Model\Response;

use CXml\Model\ResponseInterface;
use CXml\Model\Url;
use JMS\Serializer\Annotation as Ser;

class PunchoutSetupResponse implements ResponseInterface
{
	/**
	 * @Ser\SerializedName("StartPage")
	 */
	private Url $startPage;

	public function __construct(Url $startPage)
	{
		$this->startPage = $startPage;
	}
}
