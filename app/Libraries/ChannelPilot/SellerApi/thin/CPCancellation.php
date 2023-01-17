<?php
namespace App\Libraries\ChannelPilot\SellerApi\Thin;

/**
 * Holds information about a cancellation.
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class CPCancellation {

	/**
	 * The header of the order required to identify your order.
	 * @var type CPOrderHeader
	 */
	public $orderHeader;

	/**
	 * @var type string
	 */
	public $cancellationTime;

	/**
	 * @var type boolean
	 */
	public $isWholeOrderCancelled;

	/**
	 * return-tracking-number for this delivery
	 * @var type String
	 */
	public $returnTrackingNumber;

	/**
	 * @var type CPOrderItem[]
	 */
	public $cancelledItems = array();

	function __construct($order_id_external, $orderId, $statusIdBefore, $source, $cancellationTime, $isWholeOrderCancelled, $returnTrackingNumber) {
		$this->orderHeader = new CPOrderHeader ($order_id_external, $orderId, $source, $isWholeOrderCancelled ? CPOrderStatus::ID_CANCELLED : $statusIdBefore, false, null, null);
		$this->cancellationTime = $cancellationTime;
		$this->isWholeOrderCancelled = $isWholeOrderCancelled;
		$this->returnTrackingNumber = $returnTrackingNumber;
	}
}

?>
