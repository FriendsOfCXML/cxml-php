<?php

namespace Mathielen\CXml\Model\Request;

use Assert\Assertion;
use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\AddressWrapper;
use Mathielen\CXml\Model\Comment;
use Mathielen\CXml\Model\MoneyWrapper;
use Mathielen\CXml\Model\Option;

class OrderRequestHeader
{
    public const TYPE_NEW = 'new';

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("orderID")
     */
    private string $orderId;

    /**
     * @Ser\XmlAttribute
     * @Ser\SerializedName("orderDate")
     */
    private \DateTime $orderDate;

    /**
     * @Ser\XmlAttribute
     */
    private string $type = self::TYPE_NEW;

    /**
     * @Ser\XmlElement
     * @Ser\SerializedName("Total")
     */
    private MoneyWrapper $total;

    /**
     * @Ser\XmlElement
     * @Ser\SerializedName("ShipTo")
     */
    private ?AddressWrapper $shipTo;

    /**
     * @Ser\XmlElement
     * @Ser\SerializedName("BillTo")
     */
    private AddressWrapper $billTo;

    /**
     * @Ser\XmlList(inline=true, entry="Comments")
     * @Ser\Type("array<Mathielen\CXml\Model\Comment>")
     *
     * @var Comment[]
     */
    private ?array $comments;

    public function __construct(
        string $orderId,
        \DateTime $orderDate,
        ?AddressWrapper $shipTo,
        AddressWrapper $billTo,
        ?array $comments,
        MoneyWrapper $total,
        string $type = self::TYPE_NEW
    )
    {
    	if ($comments) {
			Assertion::allIsInstanceOf($comments, Comment::class);
    	}

        $this->orderId = $orderId;
        $this->orderDate = $orderDate;
        $this->type = $type;
        $this->total = $total;
        $this->shipTo = $shipTo;
        $this->billTo = $billTo;
        $this->comments = $comments;
    }
}
