<?php

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

class Status
{
    #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
    private ?string $lang = null;

    #[Serializer\XmlAttribute]
    private int $code;

    #[Serializer\XmlAttribute]
    private string $text;

    #[Serializer\XmlValue(cdata: false)]
    private ?string $message = null;

    public function __construct(int $code = 200, string $text = 'OK', string $message = null, string $lang = null)
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
