<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Status
{
    /**
     * @Ser\XmlAttribute
     */
    private int $code;

    /**
     * @Ser\XmlAttribute
     */
    private string $text;

    /**
     * @Ser\XmlValue()
     */
    private ?string $message;

    public function __construct(int $code, string $text, ?string $message = null)
    {
        $this->code = $code;
        $this->text = $text;
        $this->message = $message;
    }
}
