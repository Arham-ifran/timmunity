<?php
namespace App\Libraries\ChannelPilot\SellerApi\Thin;
/**
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class CPOrderSummary {

	/**
	 * @var type string
	 */
	public $currencyIso3;

	/**
	 * @var type CPMoney
	 */
	public $totalSumItems;

	/**
	 * @var type CPMoney
	 */
	public $totalSumItemsInclDiscount;

	/**
	 * @var type CPMoney
	 */
	public $totalSumOrder;

	/**
	 * @var type CPMoney
	 */
	public $totalSumOrderInclDiscount;

	/**
	 * @var type string
	 */
	public $message;

	/**
	 * @var type Number
	 */
	public $feeTotalNet;
}

?>
