<?php
namespace App\Libraries\ChannelPilot\SellerApi\Thin;
/**
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class CPOrderItem {

	/**
	 * @var type string
	 */
	public $id;

	/**
	 * @var type string
	 */
	public $idExternal;

	/**
	 * @var type CPArticle
	 */
	public $article;

	/**
	 * @var type int
	 */
	public $quantityOrdered;

	/**
	 * @var type int
	 */
	public $quantityDelivered;

	/**
	 * @var type int
	 */
	public $quantityCancelled;

	/**
	 * @var type CPMoney
	 */
	public $costsSingle;

	/**
	 * @var type CPMoney
	 */
	public $costsTotal;

	/**
	 * @var type number
	 */
	public $feeSingleNet;

	/**
	 * @var type number
	 */
	public $feeTotalNet;

	/**
	 * @var type CPMoney
	 */
	public $discountSingle;

	/**
	 * @var type CPMoney
	 */
	public $discountTotal;

	/**
	 * @var type string
	 */
	public $expectedShippingTimeFrom;

	/**
	 * @var type string
	 */
	public $expectedShippingTimeTo;
}

?>
