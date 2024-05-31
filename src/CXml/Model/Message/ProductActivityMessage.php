<?php

namespace CXml\Model\Message;

use CXml\Model\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Serializer;

#[Serializer\AccessorOrder(order: 'custom', custom: ['productActivityHeader', 'productActivityDetails', 'extrinsics'])]
class ProductActivityMessage implements MessagePayloadInterface
{
    use ExtrinsicsTrait;

    #[Serializer\SerializedName('ProductActivityHeader')]
    private ProductActivityHeader $productActivityHeader;

    /**
     * @var ProductActivityDetail[]
     */
    #[Serializer\XmlList(entry: 'ProductActivityDetails', inline: true)]
    #[Serializer\Type('array<CXml\Model\Message\ProductActivityDetail>')]
    private array $productActivityDetails = [];

    private function __construct(string $messageId, string $processType = null, \DateTimeInterface $creationDate = null)
    {
        $this->productActivityHeader = new ProductActivityHeader($messageId, $processType, $creationDate);
    }

    public static function create(string $messageId, string $processType = null, \DateTimeInterface $creationDate = null): self
    {
        return new self($messageId, $processType, $creationDate);
    }

    public function addProductActivityDetail(ProductActivityDetail $productActivityDetail): self
    {
        $this->productActivityDetails[] = $productActivityDetail;

        return $this;
    }

    public function getProductActivityHeader(): ProductActivityHeader
    {
        return $this->productActivityHeader;
    }

    public function getProductActivityDetails(): array
    {
        return $this->productActivityDetails;
    }
}
