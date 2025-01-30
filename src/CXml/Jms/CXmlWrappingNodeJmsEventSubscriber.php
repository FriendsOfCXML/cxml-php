<?php

declare(strict_types=1);

namespace CXml\Jms;

use CXml\Model\Exception\CXmlModelNotFoundException;
use CXml\Model\Extension\PaymentReference;
use CXml\Model\ExtensionInterface;
use CXml\Model\Message\Message;
use CXml\Model\Payment;
use CXml\Model\Request\Request;
use CXml\Model\Response\Response;
use FilesystemIterator;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\XmlSerializationVisitor;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RuntimeException;
use SimpleXMLElement;
use SplFileInfo;

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

    private static array $allModelClasses;

    private static function isModelClass(string $className): bool
    {
        return
            str_starts_with($className, 'CXml\Model\\')
            || in_array(ExtensionInterface::class, class_implements($className));
    }

    private static function getOrFindAllModelClasses(): array
    {
        if (isset(self::$allModelClasses)) {
            return self::$allModelClasses;
        }

        self::$allModelClasses = [];

        $pathToModelFiles = realpath(__DIR__ . '/../Model');
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pathToModelFiles, FilesystemIterator::SKIP_DOTS));

        /** @var SplFileInfo $file */
        foreach ($rii as $file) {
            if ('php' !== $file->getExtension()) {
                continue;
            }

            $subNamespace = substr($file->getPath(), strlen($pathToModelFiles));
            $subNamespace = str_replace('/', '\\', $subNamespace);

            $className = 'CXml\Model' . $subNamespace . '\\' . $file->getBasename('.php');
            if (!self::isModelClass($className)) {
                continue;
            }

            $shortName = (new ReflectionClass($className))->getShortName();

            if (isset(self::$allModelClasses[$shortName])) {
                throw new RuntimeException('Duplicate short class name: ' . $shortName);
            }

            self::$allModelClasses[$shortName] = $className;
        }

        return self::$allModelClasses;
    }

    public static function isEligible(string $incomingType): bool
    {
        return in_array($incomingType, self::$mainPayloadClasses, true);
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
        /** @var XmlSerializationVisitor $visitor */
        $visitor = $event->getVisitor();

        /** @var Payment $payment */
        $payment = $event->getObject();

        $paymentImplList = $payment->paymentImpl;

        if (!is_array($paymentImplList)) {
            $paymentImplList = [$paymentImplList];
        }

        foreach ($paymentImplList as $impl) {
            $cls = (new ReflectionClass($impl))->getShortName();

            $visitor->visitProperty(
                new StaticPropertyMetadata(Payment::class, $cls, null),
                $impl,
            );
        }
    }

    /**
     * @throws CXmlModelNotFoundException
     */
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

        $isArray = count($data->children()) > 1;
        if ($isArray) {
            $propertyMetadata->setType([
                'name' => 'array',
                'params' => [
                    PaymentReference::class,
                ],
            ]);
        } else {
            $payloadNode = $data->children()[0];
            $serializedName = $payloadNode->getName();
            $cls = $this->findClassForSerializedName($serializedName);

            $propertyMetadata->serializedName = $serializedName;
            $propertyMetadata->setType([
                'name' => $cls,
                'params' => [],
            ]);
        }

        $metadata->addPropertyMetadata($propertyMetadata);
    }

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
        $payload = $event->getObject()->payload ?? null;

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
        $cls = $this->findClassForSerializedName($serializedName);

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

    /**
     * @throws CXmlModelNotFoundException
     */
    private function findClassForSerializedName(string $serializedName): string
    {
        $modelClasses = self::getOrFindAllModelClasses();

        return $modelClasses[$serializedName] ?? throw new CXmlModelNotFoundException($serializedName);
    }
}
