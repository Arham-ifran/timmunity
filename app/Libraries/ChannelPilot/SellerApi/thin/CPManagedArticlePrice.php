<?php
namespace App\Libraries\ChannelPilot\SellerApi\Thin;

/**
 * Holds an address. e.g. a shipping-address
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class CPManagedArticlePrice {

	/**
	 * @var type CPArticle
	 */
	public $article;

	/**
	 * @var type number
	 */
	public $price;

	/**
	 * @var type string
	 */
	public $lastUpdate;

	function __construct($article, $price,$lastUpdate) {
		$this->article=$article;
		$this->price=$price;
		$this->lastUpdate=$lastUpdate;
	}
}

?>
