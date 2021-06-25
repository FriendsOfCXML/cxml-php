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
     * @Ser\XmlValue(
     *     cdata=false
     * )
     */
    private ?string $message;

    public function __construct(int $code, string $text, ?string $message = null)
    {
        $this->code = $code;
        $this->text = $text;
        $this->message = $message;
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
