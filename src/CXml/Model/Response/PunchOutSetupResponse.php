<?php

namespace CXml\Model\Response;

use CXml\Model\Url;
use JMS\Serializer\Annotation as Serializer;

class PunchOutSetupResponse implements ResponsePayloadInterface
{
    #[Serializer\SerializedName('StartPage')]
    private Url $startPage;

    public function __construct(Url $startPage)
    {
        $this->startPage = $startPage;
    }
}
