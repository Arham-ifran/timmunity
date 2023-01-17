<?php
namespace App\Libraries\ChannelPilot\SellerApi\Thin;
/**
 * Header for a reponse.
 * @author Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class CPResponseHeader {
  /**
   * Every request returns a defined result code. @see CPResultCodes
   * @var type int
   */
  public $resultCode;
  /**
   * the message could provide further information about the result.
   * @var type string
   */
  public $resultMessage;
}

?>
