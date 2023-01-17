<?php
namespace App\Libraries\ChannelPilot\SellerApi\Thin;
/**
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class CPPayment {

	/**
	 * @var type string
	 */
	public $typeId;

	/**
	 * @var type string
	 */
	public $typeTitle;

	/**
	 * @var type CPMoney
	 */
	public $costs;

	/**
	 * @var type string
	 */
	public $paymentTime;
}

?>
