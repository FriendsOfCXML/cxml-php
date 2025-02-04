<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\Trait\ExtrinsicsTrait;
use CXml\Model\Trait\IdReferencesTrait;
use DateTime;
use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['idReferences', 'extrinsics'])]
class ConfirmationHeader
{
    use ExtrinsicsTrait;
    use IdReferencesTrait;

    final public const TYPE_ACCEPT = 'accept';

    final public const TYPE_ALLDETAIL = 'allDetail';

    final public const TYPE_DETAIL = 'detail';

    final public const TYPE_BACKORDERED = 'backordered';

    final public const TYPE_EXCEPT = 'except';

    final public const TYPE_REJECT = 'reject';

    final public const TYPE_REQUESTTOPAY = 'requestToPay';

    final public const TYPE_REPLACE = 'replace';

    public function __construct(
        #[Serializer\SerializedName('type')]
        #[Serializer\XmlAttribute]
        public readonly string $type,
        #[Serializer\XmlAttribute]
        public readonly DateTimeInterface $noticeDate = new DateTime(),
    ) {
        Assertion::inArray($type, [
            self::TYPE_ACCEPT,
            self::TYPE_ALLDETAIL,
            self::TYPE_DETAIL,
            self::TYPE_BACKORDERED,
            self::TYPE_EXCEPT,
            self::TYPE_REJECT,
            self::TYPE_REQUESTTOPAY,
            self::TYPE_REPLACE,
        ]);
    }

    public static function create(string $type, DateTimeInterface $noticeDate = new DateTime()): self
    {
        return new self(
            $type,
            $noticeDate,
        );
    }
}
