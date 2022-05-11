<?php

namespace Mathielen\CXml\Model\Message;

use JMS\Serializer\Annotation as Ser;
use Mathielen\CXml\Model\MoneyWrapper;

class PunchOutOrderMessageHeader
{
	public const OPERATION_CREATE = 'create';
	public const OPERATION_EDIT = 'edit';
	public const OPERATION_INSPECT = 'inspect';

	/**
	 * @Ser\XmlAttribute
	 */
	private ?string $operationAllowed;

	/**
	 * @Ser\SerializedName("Total")
	 */
	private MoneyWrapper $total;

	public function __construct(MoneyWrapper $total, ?string $operationAllowed = null)
	{
		$this->total = $total;
		$this->operationAllowed = $operationAllowed ?? self::OPERATION_CREATE;
	}
}
