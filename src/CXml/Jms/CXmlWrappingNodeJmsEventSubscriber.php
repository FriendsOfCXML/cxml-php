<?php

declare(strict_types=1);

namespace CXml\Jms;

use CXml\Model\Exception\CXmlModelNotFoundException;
use CXml\Model\Message\Message;
use CXml\Model\Payment;
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
use ReflectionClass;
use ReflectionException;
use SimpleXMLElement;

use function class_exists;

/**
 * Certain CXml-nodes have "wrappers"-nodes which this subscriber automatically handles.
 */
class CXmlWrappingNodeJmsEventSubscriber implements EventSubscriberInterface
{
    private static array $mainPayloadClasses = [
        Message::class,
        Request::class,
        Response::class,
    ];

    public static function isEligible(string $incomingType): bool
    {
        return in_array($incomingType, self::$mainPayloadClasses);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializeCXmlMainPayload',
                'format' => 'xml',
            ],
            [
                'event' => Events::PRE_DESERIALIZE,
                'method' => 'onPreDeserializeCXmlMainPayload',
                'format' => 'xml',
            ],

            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializePayment',
                'class' => Payment::class,
                'format' => 'xml',
            ],
            [
                'event' => Events::PRE_DESERIALIZE,
                'method' => 'onPreDeserializePayment',
                'class' => Payment::class,
                'format' => 'xml',
            ],
        ];
    }

    private function findPayloadNode(SimpleXMLElement $cXmlNode): ?SimpleXMLElement
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

    public function onPostSerializePayment(ObjectEvent $event): void
    {
        $visitor = $event->getVisitor();

        $paymentImpl = $event->getObject()->getPaymentImpl();

        $cls = (new ReflectionClass($paymentImpl))->getShortName();

        $visitor->visitProperty(
            new StaticPropertyMetadata(Payment::class, $cls, null),
            $paymentImpl,
        );
    }

    public function onPreDeserializePayment(PreDeserializeEvent $event): void
    {
        /** @var ClassMetadata $metadata */
        $metadata = $event->getContext()->getMetadataFactory()->getMetadataForClass(Payment::class);

        // manipulate metadata of payload on-the-fly to match xml

        $propertyMetadata = new PropertyMetadata(
            Payment::class,
            'paymentImpl',
        );

        /** @var SimpleXMLElement $data */
        $data = $event->getData();
        $payloadNode = $this->findPayloadNode($data);
        if (!$payloadNode instanceof SimpleXMLElement) {
            return;
        }

        $serializedName = $payloadNode->getName();
        $targetNamespace = (new ReflectionClass(Payment::class))->getNamespaceName();

        $cls = $targetNamespace . '\Extension\\' . $serializedName;
        if (!class_exists($cls)) {
            throw new CXmlModelNotFoundException($serializedName);
        }

        $propertyMetadata->serializedName = $serializedName;
        $propertyMetadata->setType([
            'name' => $cls,
            'params' => [],
        ]);

        $metadata->addPropertyMetadata($propertyMetadata);
    }

    /**
     * @throws ReflectionException
     */
    public function onPostSerializeCXmlMainPayload(ObjectEvent $event): void
    {
        $incomingType = $event->getType()['name'];
        if (!self::isEligible($incomingType)) {
            return;
        }

        /** @var XmlSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        // this is the actual payload object of type MessagePayloadInterface
        /** @phpstan-ignore-next-line */
        $payload = $event->getObject()->getPayload();

        if ($payload) {
            $cls = (new ReflectionClass($payload))->getShortName();

            // tell jms to add the payload value in a wrapped node
            $visitor->visitProperty(
                new StaticPropertyMetadata($incomingType, $cls, null),
                $payload,
            );
        }
    }

    /**
     * @throws CXmlModelNotFoundException
     * @throws ReflectionException
     */
    public function onPreDeserializeCXmlMainPayload(PreDeserializeEvent $event): void
    {
        $incomingType = $event->getType()['name'];
        if (!self::isEligible($incomingType)) {
            return;
        }

        /** @var ClassMetadata $metadata */
        $metadata = $event->getContext()->getMetadataFactory()->getMetadataForClass($incomingType);

        /** @var SimpleXMLElement $data */
        $data = $event->getData();
        $payloadNode = $this->findPayloadNode($data);
        if (!$payloadNode instanceof SimpleXMLElement) {
            return;
        }

        $serializedName = $payloadNode->getName();
        $targetNamespace = (new ReflectionClass($incomingType))->getNamespaceName();

        $cls = $targetNamespace . '\\' . $serializedName;
        if (!class_exists($cls)) {
            throw new CXmlModelNotFoundException($serializedName);
        }

        // manipulate metadata of payload on-the-fly to match xml

        $propertyMetadata = new PropertyMetadata(
            $incomingType,
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
