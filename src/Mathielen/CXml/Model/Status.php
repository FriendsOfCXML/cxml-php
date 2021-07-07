<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class Status
{
    /**
     * @Ser\XmlAttribute(namespace="http://www.w3.org/XML/1998/namespace")
     */
    private ?string $lang = null;

    /**
     * @Ser\XmlAttribute
     */
    private int $code;

    /**
     * @Ser\XmlAttribute
     */
    private string $text;

    /**
     * @Ser\XmlValue(cdata=false)
     */
    private ?string $message;

    public function __construct(int $code, string $text, ?string $message = null, ?string $lang = null)
    {
        $this->code = $code;
        $this->text = $text;
        $this->message = $message;
        $this->lang = $lang;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
