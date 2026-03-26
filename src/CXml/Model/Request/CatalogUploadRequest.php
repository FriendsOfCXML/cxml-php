<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\Description;
use CXml\Model\MultilanguageString;
use CXml\Model\Notification;
use CXml\Model\Url;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['catalogName', 'description', 'attachment', 'notification'])]
class CatalogUploadRequest implements RequestPayloadInterface
{
    #[Serializer\SerializedName('Attachment')]
    public Url $attachment;

    public function __construct(
        #[Serializer\XmlAttribute]
        #[Serializer\SerializedName('operation')]
        public readonly string $operation,
        #[Serializer\SerializedName('CatalogName')]
        public MultilanguageString $catalogName,
        #[Serializer\SerializedName('Description')]
        public Description $description,
        string $attachmentUrl,
        #[Serializer\SerializedName('Notification')]
        public Notification $notification = new Notification(),
    ) {
        Assertion::inArray($this->operation, ['new', 'update']);

        $this->attachment = new Url($attachmentUrl);
    }
}
