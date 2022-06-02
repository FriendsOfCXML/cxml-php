<?php

namespace CXml\Model\Message;

use CXml\Model\ExtrinsicsTrait;
use JMS\Serializer\Annotation as Ser;

class ProductActivityMessage implements MessagePayloadInterface
{
	use ExtrinsicsTrait;

	/**
	 * @Ser\SerializedName("ProductActivityHeader")
	 */
	private ProductActivityHeader $productActivityHeader;

	/**
	 * @Ser\XmlList(inline=true, entry="ProductActivityDetails")
	 * @Ser\Type("array<CXml\Model\Message\Message\ProductActivityDetail>")
	 *
	 * @var ProductActivityDetail[]
	 */
	private array $productActivityDetails = [];

	private function __construct(string $messageId, ?string $processType = null, \DateTime $creationDate = null)
	{
		$this->productActivityHeader = new ProductActivityHeader($messageId, $processType, $creationDate);
	}

	public static function create(string $messageId, ?string $processType = null, \DateTime $creationDate = null): self
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
