<?php
namespace App\Libraries\ChannelPilot\SellerApi\Responses;

/**
 * GetNewMarketplaceOrdersResponse.
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class GetNewMarketplaceOrdersResponse extends Response {
	/**
	 * are more orders available, than could be returned in this call
	 * @var type boolean
	 */
	public $moreAvailable;
	/**
	 * in case there are more orders avalaible, this is a count for it
	 * @var type integer
	 */
	public $countMoreAvailable;

	/**
	 * array of new orders, can be empty
	 * @var type CPOrder[]
	 */
	public $orders  = array();
}

?>
