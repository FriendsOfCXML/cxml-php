<?php

declare(strict_types=1);

namespace CXml\Model\Request;

use Assert\Assertion;
use CXml\Model\ExtrinsicsTrait;
use CXml\Model\IdReferencesTrait;
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
        private readonly string $type,
        #[Serializer\XmlAttribute]
        private ?\DateTimeInterface $noticeDate = null,
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

        $this->noticeDate = $noticeDate ?? new \DateTime();
    }

    public static function create(string $type, \DateTimeInterface $noticeDate = null): self
    {
        return new self(
            $type,
            $noticeDate,
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNoticeDate(): ?\DateTimeInterface
    {
        return $this->noticeDate;
    }
}
