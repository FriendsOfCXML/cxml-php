<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Comment
{
    /**
     * @Ser\SerializedName("Attachment")
     */
    private ?Url $attachment;

    /**
     * @Ser\XmlValue(cdata=false)
     */
    private ?string $value;

    public function __construct(?string $value = null, ?Url $attachment = null)
    {
        $this->attachment = $attachment;
        $this->value = $value;
    }
}
