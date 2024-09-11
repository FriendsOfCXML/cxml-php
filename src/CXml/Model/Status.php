<?php

declare(strict_types=1);

namespace CXml\Model;

use JMS\Serializer\Annotation as Serializer;

readonly class Status
{
    public function __construct(
        #[Serializer\XmlAttribute]
        private int $code = 200,
        #[Serializer\XmlAttribute]
        private string $text = 'OK',
        #[Serializer\XmlValue(cdata: false)]
        private ?string $message = null,
        #[Serializer\XmlAttribute(namespace: 'http://www.w3.org/XML/1998/namespace')]
        private ?string $lang = null,
    ) {
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

    public function getLang(): ?string
    {
        return $this->lang;
    }
}
