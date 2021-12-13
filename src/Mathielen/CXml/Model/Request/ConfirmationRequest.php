<?php

namespace Mathielen\CXml\Model\Request;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\OrderReference;
use Mathielen\CXml\Model\RequestInterface;

class ConfirmationRequest implements RequestInterface
{
	/**
	 * @Ser\SerializedName("ConfirmationHeader")
	 */
	private ConfirmationHeader $confirmationHeader;

	/**
	 * @Ser\SerializedName("OrderReference")
	 */
	private OrderReference $orderReference;

	public function __construct(ConfirmationHeader $confirmationHeader, OrderReference $orderReference)
	{
		$this->confirmationHeader = $confirmationHeader;
		$this->orderReference = $orderReference;
	}

	public function getConfirmationHeader(): ConfirmationHeader
	{
		return $this->confirmationHeader;
	}

	public function getOrderReference(): OrderReference
	{
		return $this->orderReference;
	}
}
