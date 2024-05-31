<?php

declare(strict_types=1);

namespace CXml\Jms;

use CXml\Model\Exception\CXmlModelNotFoundException;
use CXml\Model\Message\Message;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\XmlSerializationVisitor;
use Metadata\ClassMetadata;

/**
 * Certain CXml-nodes have "wrappers"-nodes which this subscriber automatically handles.
 */
class CXmlWrappingNodeJmsEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializePayload',
                'class' => Message::class,
                'format' => 'xml',
            ],
            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializePayload',
                'class' => Request::class,
                'format' => 'xml',
            ],
            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializePayload',
                'class' => Response::class,
                'format' => 'xml',
            ],

            [
                'event' => Events::PRE_DESERIALIZE,
                'method' => 'onPreDeserializePayload',
                'class' => Message::class,
                'format' => 'xml',
            ],
            [
                'event' => Events::PRE_DESERIALIZE,
                'method' => 'onPreDeserializePayload',
                'class' => Request::class,
                'format' => 'xml',
            ],
            [
                'event' => Events::PRE_DESERIALIZE,
                'method' => 'onPreDeserializePayload',
                'class' => Response::class,
                'format' => 'xml',
            ],
        ];
    }

    private function findPayloadNode(\SimpleXMLElement $cXmlNode): ?\SimpleXMLElement
    {
        foreach ($cXmlNode->children() as $child) {
            if ('Status' === $child->getName()) {
                continue;
            }

            // first child if not 'Status'
            return $child;
        }

        return null;
    }

    /**
     * @throws \ReflectionException
     */
    public function onPostSerializePayload(ObjectEvent $event): void
    {
        /** @var XmlSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        // this is the actual payload object of type MessagePayloadInterface
        /** @phpstan-ignore-next-line */
        $payload = $event->getObject()->getPayload();

        if ($payload) {
            $cls = (new \ReflectionClass($payload))->getShortName();

            // tell jms to add the payload value in a wrapped node
            $visitor->visitProperty(
                new StaticPropertyMetadata($event->getType()['name'], $cls, null),
                $payload,
            );
        }
    }

    /**
     * @throws CXmlModelNotFoundException
     * @throws \ReflectionException
     */
    public function onPreDeserializePayload(PreDeserializeEvent $event): void
    {
        /** @var ClassMetadata $metadata */
        $metadata = $event->getContext()->getMetadataFactory()->getMetadataForClass($event->getType()['name']);

        /** @var \SimpleXMLElement $data */
        $data = $event->getData();
        $payloadNode = $this->findPayloadNode($data);
        if (!$payloadNode instanceof \SimpleXMLElement) {
            return;
        }

        $serializedName = $payloadNode->getName();
        $targetNamespace = (new \ReflectionClass($event->getType()['name']))->getNamespaceName();

        $cls = $targetNamespace . '\\' . $serializedName;
        if (!\class_exists($cls)) {
            throw new CXmlModelNotFoundException($serializedName);
        }

        // manipulate metadata of payload on-the-fly to match xml

        $propertyMetadata = new PropertyMetadata(
            $event->getType()['name'],
            'payload',
        );

        $propertyMetadata->serializedName = $serializedName;
        $propertyMetadata->setType([
            'name' => $cls,
            'params' => [],
        ]);

        $metadata->addPropertyMetadata($propertyMetadata);
    }
}
