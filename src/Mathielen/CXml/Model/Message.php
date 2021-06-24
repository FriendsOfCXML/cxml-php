<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Message implements PayloadInterface
{
    /**
     * @Ser\SerializedName("Status")
     */
    private Status $status;

    /**
     * @Ser\XmlAttribute
     */
    private string $deploymentMode;

    /**
     * @Ser\XmlAttribute
     */
    private string $inReplyTo;

    /**
     * @Ser\XmlAttribute
     */
    private string $id;

    public function __construct(string $id, string $deploymentMode = 'production', string $inReplyTo = null)
    {
        $this->deploymentMode = $deploymentMode;
        $this->inReplyTo = $inReplyTo;
        $this->id = $id;
    }
}
