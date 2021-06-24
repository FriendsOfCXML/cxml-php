<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Party
{
    /**
     * @Ser\SerializedName("Credential")
     */
    private Credential $credential;

    /**
     * @Ser\SerializedName("UserAgent")
     */
    private ?string $userAgent;

    public function __construct(Credential $credential, ?string $userAgent = null)
    {
        $this->credential = $credential;
        $this->userAgent = $userAgent;
    }
}
