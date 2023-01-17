<?php
namespace App\Libraries\ChannelPilot\SellerApi\Responses;

/**
 * GetManagedArticlePricesResponse.
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class GetManagedArticlePricesResponse extends Response {

	public $moreAvailable;
	public $countMoreAvailable;
	/**
	 * array of managed article prices, can be empty
	 * @var type CPManagedArticlePrice[]
	 */
	public $managedArticlePrices  = array();
}

?>
