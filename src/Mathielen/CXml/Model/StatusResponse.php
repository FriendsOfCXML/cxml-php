<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class StatusResponse implements ResponseInterface
{
    /**
     * @Ser\SerializedName("Status")
     */
    private Status $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }
}
