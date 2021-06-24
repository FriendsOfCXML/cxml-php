<?php

namespace Mathielen\CXml\Jms;

use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use Mathielen\CXml\Model\Message;
use Mathielen\CXml\Model\Request;
use Mathielen\CXml\Model\Response;
use Mathielen\CXml\Model\ResponseInterface;

class JmsEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializeMessage',
                'class' => Message::class,
                'format' => 'xml',
            ],
            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializeRequest',
                'class' => Request::class,
                'format' => 'xml',
            ],
            [
                'event' => Events::POST_SERIALIZE,
                'method' => 'onPostSerializeResponse',
                'class' => ResponseInterface::class,
                'format' => 'xml',
            ],
        ];
    }

    public function onPostSerializeMessage(ObjectEvent $event): void
    {
        $visitor  = $event->getVisitor();

        //this is the actual message object of type MessageInterface
        $message = $event->getObject()->getMessage();
        $cls = (new \ReflectionClass($message))->getShortName();

        $visitor->visitProperty(
            new StaticPropertyMetadata(Message::class, $cls, null),
            $message
        );
    }

    public function onPostSerializeRequest(ObjectEvent $event): void
    {
        $visitor  = $event->getVisitor();

        //this is the actual message object of type MessageInterface
        $request = $event->getObject()->getRequest();
        $cls = (new \ReflectionClass($request))->getShortName();

        $visitor->visitProperty(
            new StaticPropertyMetadata(Request::class, $cls, null),
            $request
        );
    }

    public function onPostSerializeResponse(ObjectEvent $event): void
    {
        $visitor  = $event->getVisitor();

        //this is the actual message object of type MessageInterface
        $response = $event->getObject()->getResponse();
        $cls = (new \ReflectionClass($response))->getShortName();

        $visitor->visitProperty(
            new StaticPropertyMetadata(Response::class, $cls, null),
            $response
        );
    }
}
