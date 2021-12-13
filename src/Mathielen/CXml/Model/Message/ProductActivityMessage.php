<?php

namespace Mathielen\CXml\Model\Message;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\Extrinsic;
use Mathielen\CXml\Model\MessageInterface;

class ProductActivityMessage implements MessageInterface
{
	/**
	 * @Ser\SerializedName("ProductActivityHeader")
	 */
	private ProductActivityHeader $productActivityHeader;

	/**
	 * @Ser\XmlList(inline=true, entry="ProductActivityDetails")
	 * @Ser\Type("array<Mathielen\CXml\Model\Message\ProductActivityDetail>")
	 *
	 * @var ProductActivityDetail[]
	 */
	private array $productActivityDetails = [];

	/**
	 * @Ser\XmlList(inline=true, entry="Extrinsic")
	 * @Ser\Type("array<Mathielen\CXml\Model\Extrinsic>")
	 *
	 * @var Extrinsic[]
	 */
	private array $extrinsics = [];

	public function __construct(string $messageId, ?string $processType = null, \DateTime $creationDate = null)
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

	public function addExtrinsic(Extrinsic $extrinsic): self
	{
		$this->extrinsics[] = $extrinsic;

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

	public function getExtrinsics(): array
	{
		return $this->extrinsics;
	}
}
