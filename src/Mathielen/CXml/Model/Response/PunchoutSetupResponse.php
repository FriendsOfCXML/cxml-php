<?php

namespace Mathielen\CXml\Model\Response;

use Mathielen\CXml\Model\ResponseInterface;
use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Url;

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
