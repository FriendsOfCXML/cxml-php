<?php

namespace Mathielen\CXml\Model;

use JMS\Serializer\Annotation as Ser;

class MultilanguageString
{
    /**
     * @Ser\XmlValue(cdata=false)
     */
    private string $value;

    /**
     * @Ser\XmlAttribute(namespace="http://www.w3.org/XML/1998/namespace")
     */
    private string $lang;

    public function __construct(string $value, string $lang = 'en')
    {
        $this->value = $value;
        $this->lang = $lang;
    }
}
