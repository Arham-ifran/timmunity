<?php
namespace App\Libraries\ChannelPilot\SellerApi;

// include the stub-classes
use App\Libraries\ChannelPilot\SellerApi\Thin\CPAuth;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPResponseHeader;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPAddress;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPArticle;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPCancellation;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPManagedArticlePrice;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPMoney;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPPayment;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPCustomer;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPCustomerGroup;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPDelivery;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderItem;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPShipping;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderStatus;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderHeader;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPOrder;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderSummary;
use App\Libraries\ChannelPilot\SellerApi\Thin\CPRefund;

use App\Libraries\ChannelPilot\SellerApi\Responses\Response;
use App\Libraries\ChannelPilot\SellerApi\Responses\GetServerTimeResponse;
use App\Libraries\ChannelPilot\SellerApi\Responses\UpdateOrdersResponse;
use App\Libraries\ChannelPilot\SellerApi\Responses\UpdateOrderResult;
use App\Libraries\ChannelPilot\SellerApi\Responses\GetNewMarketplaceOrdersResponse;
use App\Libraries\ChannelPilot\SellerApi\Responses\GetManagedArticlePricesResponse;
use SoapClient;
use SoapParam;

/**
 * Main API-Class
 * @author    Channel Pilot Solutions GmbH <api@channelpilot.com>
 * @version 4.1
 */
class ChannelPilotSellerAPI_v4_1 extends SoapClient {

    private $auth;
    private $wsdlUrl = 'https://seller.api.channelpilot.com/4_1?wsdl';
    private $soapOptions = array(
        'connection_timeout' => 20,
        'features' => SOAP_SINGLE_ELEMENT_ARRAYS
    );
    private $classmap = array(
        'CPAuth' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPAuth',
        'CPResponseHeader' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPResponseHeader',
        'AbstractResponse' => 'AbstractResponse',
        'GetServerTimeResponse' => 'App\Libraries\ChannelPilot\SellerApi\Responses\GetServerTimeResponse',
        'CPArticleUpdate' => 'CPArticleUpdate',
        'UpdateArticlesResponse' => 'UpdateArticlesResponse',
        'UpdateArticleResult' => 'UpdateArticleResult',
        'UpdateOrdersResponse' => 'App\Libraries\ChannelPilot\SellerApi\Responses\UpdateOrdersResponse',
        'UpdateOrderResult' => 'App\Libraries\ChannelPilot\SellerApi\Responses\UpdateOrderResult',
        'CPAddress' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPAddress',
        'CPArticle' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPArticle',
        'CPManagedArticlePrice' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPManagedArticlePrice',
        'CPMoney' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPMoney',
        'CPPayment' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPPayment',
        'CPCustomer' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPCustomer',
        'CPOrderItem' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderItem',
        'CPShipping' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPShipping',
        'CPOrderStatus' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderStatus',
        'CPOrderHeader' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderHeader',
        'CPOrder' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPOrder',
        'CPOrderSummary' => 'App\Libraries\ChannelPilot\SellerApi\Thin\CPOrderSummary',
        'GetNewMarketplaceOrdersResponse' => 'App\Libraries\ChannelPilot\SellerApi\Responses\GetNewMarketplaceOrdersResponse',
        'GetManagedArticlePricesResponse' => 'App\Libraries\ChannelPilot\SellerApi\Responses\GetManagedArticlePricesResponse',
        'CPRefund' => 'CPRefund'
    );

    public function __construct($merchantId, $shopToken) {
        $this->auth = new CPAuth($merchantId, $shopToken);

        foreach ($this->classmap as $key => $value) {
            if (!isset($this->soapOptions['classmap'][$key])) {
                $this->soapOptions['classmap'][$key] = $value;
            }
        }
        parent::__construct($this->wsdlUrl, $this->soapOptions);
    }

    /**
     * Receives the acutal server time. Can be used to test the connection.
     * @return GetServerTimeResponse
     */
    public function getServerTime() {
        return $this->__call(
            'getServerTime',
            array(
                new SoapParam($this->auth, 'auth')
            )
        );
    }

    /**
     * retrieves new marketplace orders
     * @return GetNewMarketplaceOrdersResponse
     */
    public function getNewMarketplaceOrders() {
        return $this->__call(
            'getNewMarketplaceOrders',
            array(
                new SoapParam($this->auth, 'auth')
            )
        );
    }

    /**
     * update orders in ChannelPilot to "imported", generates the matching between externalOrderId and the shop-internal orderId
     * @param array $orders array of CPOrders
     * @param type $mapOrderItemIds boolean, if channelPilot should map your internal orderItemIds
     * @return type
     */
    public function setImportedOrders(array $orders, $mapOrderItemIds) {
        return $this->__call(
            'setImportedOrders',
            array(
                new SoapParam($this->auth, 'auth'),
                new SoapParam($orders, 'importedOrders'),
                new SoapParam($mapOrderItemIds, 'mapOrderItemIds')
            )
        );
    }


    public function registerDeliveries(array $deliveries) {
        return $this->__call(
            'registerDeliveries',
            array(
                new SoapParam($this->auth, 'auth'),
                new SoapParam($deliveries, 'deliveries')
            )
        );
    }

    public function registerCancellations(array $cancellations) {
        return $this->__call(
            'registerCancellations',
            array(
                new SoapParam($this->auth, 'auth'),
                new SoapParam($cancellations, 'cancellations')
            )
        );
    }

    public function getDynamicArticlePrices($priceId, $method, $filterArticles, $filterFrom) {
        return $this->__call(
            'getDynamicArticlePrices',
            array(
                new SoapParam($this->auth, 'auth'),
                new SoapParam($priceId, 'priceId'),
                new SoapParam(null, 'pagination'),
                new SoapParam($method, 'method'),
                new SoapParam($filterArticles, 'filterArticles'),
                new SoapParam($filterFrom, 'filterFrom')
            )
        );
    }

    /**
     * Set paymentTime in ChannelPilot. Send CPOrder with CPOrderHeader and CPPayment (paymentTime is necessary).
     * @param CPOrder[] $orders
     * @return UpdateOrdersResponse
     */
    public function setPaidOrders(array $orders) {
        return $this->__call(
            'setPaidOrders', array(
                new SoapParam($this->auth, 'auth'),
                new SoapParam($orders, 'paidOrders')
            )
        );
    }

    public function registerRefunds(array $refunds) {
        return $this->__call(
            'registerRefunds', array(
                new SoapParam($this->auth, 'auth'),
                new SoapParam($refunds, 'refunds')
            )
        );
    }
}
