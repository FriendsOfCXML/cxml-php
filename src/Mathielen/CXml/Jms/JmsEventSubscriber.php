<?php

namespace Mathielen\CXml\Jms;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use Mathielen\CXml\Model\Exception\CXmlModelNotFoundException;
use Mathielen\CXml\Model\Message;
use Mathielen\CXml\Model\Request;
use Mathielen\CXml\Model\Response;

class JmsEventSubscriber implements EventSubscriberInterface
{
	public static function getSubscribedEvents(): array
	{
		return [
			[
				'event' => Events::POST_SERIALIZE,
				'method' => 'onPostSerialize',
				'class' => Message::class,
				'format' => 'xml',
			],
			[
				'event' => Events::POST_SERIALIZE,
				'method' => 'onPostSerialize',
				'class' => Request::class,
				'format' => 'xml',
			],
			[
				'event' => Events::POST_SERIALIZE,
				'method' => 'onPostSerialize',
				'class' => Response::class,
				'format' => 'xml',
			],

			[
				'event' => Events::PRE_DESERIALIZE,
				'method' => 'onPreDeserialize',
				'class' => Message::class,
				'format' => 'xml',
			],
			[
				'event' => Events::PRE_DESERIALIZE,
				'method' => 'onPreDeserialize',
				'class' => Request::class,
				'format' => 'xml',
			],
			[
				'event' => Events::PRE_DESERIALIZE,
				'method' => 'onPreDeserialize',
				'class' => Response::class,
				'format' => 'xml',
			],
		];
	}

	private static function findPayloadNode(\SimpleXMLElement $cXmlNode): ?\SimpleXMLElement
	{
		foreach ($cXmlNode->children() as $child) {
			if ('Status' === $child->getName()) {
				continue;
			}

			//first child if not 'Status'
			return $child;
		}

		return null;
	}

	public function onPostSerialize(ObjectEvent $event): void
	{
		$visitor = $event->getVisitor();

		//this is the actual payload object of type MessageInterface
		$payload = $event->getObject()->getPayload();

		if ($payload) {
			$cls = (new \ReflectionClass($payload))->getShortName();

			//tell jms to add the payload value in a wrapped node
			$visitor->visitProperty(
				new StaticPropertyMetadata($event->getType()['name'], $cls, null),
				$payload
			);
		}
	}

	/**
	 * @throws CXmlModelNotFoundException
	 */
	public function onPreDeserialize(PreDeserializeEvent $event): void
	{
		$metadata = $event->getContext()->getMetadataFactory()->getMetadataForClass($event->getType()['name']);

		$payloadNode = self::findPayloadNode($event->getData());
		if (null === $payloadNode) {
			return;
		}

		$serializedName = $payloadNode->getName();

		//TODO unintuitive combination of wrapper-cls and real payload
		$cls = $event->getType()['name'].'\\'.$serializedName;
		if (!\class_exists($cls)) {
			throw new CXmlModelNotFoundException($serializedName);
		}

		//manipulate metadata of payload on-the-fly to match xml

		/** @var PropertyMetadata $propertyMetadata */
		$propertyMetadata = new PropertyMetadata(
			$event->getType()['name'],
			'payload'
		);

		$propertyMetadata->serializedName = $serializedName;
		$propertyMetadata->setType([
			'name' => $cls,
			'params' => [],
		]);

		$metadata->addPropertyMetadata($propertyMetadata);
	}
}
