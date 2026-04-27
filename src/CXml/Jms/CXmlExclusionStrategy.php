<?php

declare(strict_types=1);

namespace CXml\Jms;

use CXml\Model\Comment;
use JMS\Serializer\Context;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

class CXmlExclusionStrategy implements ExclusionStrategyInterface
{
    public function shouldSkipClass(ClassMetadata $metadata, Context $context): bool
    {
        return false;
    }

    public function shouldSkipProperty(PropertyMetadata $property, Context $context): bool
    {
        // Check if we are serializing to XML
        if ('xml' !== $context->getFormat()) {
            return false;
        }

        if (Comment::class === $property->class && 'attachment' === $property->name) {
            /** @var Comment $object */
            /** @phpstan-ignore-next-line */
            $object = $context->getObject();
            // if comment has a textual value, we dont serialize the <Attachment> element anymore. See DTD:
            // <!ELEMENT Comments ( #PCDATA | Attachment )* >
            if (null !== $object->value) {
                return true;
            }
        }

        return false;
    }
}
