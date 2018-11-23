<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MarketplaceBaseShipping
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MarketplaceBaseShipping\Helper;

/**
 * MarketplaceBaseShipping data helper.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var string
     */

    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param Magento\Framework\App\Helper\Context        $context
     * @param Magento\Catalog\Model\ResourceModel\Product $product
     * @param Magento\Store\Model\StoreManagerInterface   $_storeManager
     * @param Magento\Directory\Model\Currency            $currency
     * @param Magento\Framework\Locale\CurrencyInterface  $localeCurrency
     * @param \Magento\Customer\Model\Session             $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\Marketplace\Model\OrdersFactory $marketplaceOrderFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->marketplaceOrderFactory = $marketplaceOrderFactory;
    }
    
    public function isShippingLabelCreated($shipmentId)
    {
        $mpOrder = $this->marketplaceOrderFactory->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', ['eq' => $this->_customerSession->getCustomerId()])
            ->addFieldToFilter('shipment_id', ['eq' => $shipmentId]);

        if ($mpOrder->getSize()) {
            $data = $mpOrder->getFirstItem();
            if ($data->getShipmentLabel()) {
                return true;
            }
        }

        return false;
    }

    /**
     * get carrier code
     *
     * @return void
     */
    public function getCarrierCode($order)
    {
        $sellerOrders = $this->marketplaceOrderFactory->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', ['eq' => $this->_customerSession->getCustomerId()])
            ->addFieldToFilter('order_id', ['eq' => $order->getId()]);
        
        if ($sellerOrders->getSize() && $this->_scopeConfig->getValue('carriers/mpmultishipping/active')) {
            $dataModel = $sellerOrders->getFirstItem();
            $multiShipMethod = $dataModel->getMultishipMethod();
            $method = explode('_', $multiShipMethod);
            if (isset($method[0])) {
                return $method[0];
            }
        }
        return $order->getShippingMethod(true)->getCarrierCode();    
    }

    /**
     * get carrier code
     *
     * @return void
     */
    public function getShippingMethod($order)
    {
        $sellerOrders = $this->marketplaceOrderFactory->create()
            ->getCollection()
            ->addFieldToFilter('seller_id', ['eq' => $this->_customerSession->getCustomerId()])
            ->addFieldToFilter('order_id', ['eq' => $order->getId()]);
        
        if ($sellerOrders->getSize() && $this->_scopeConfig->getValue('carriers/mpmultishipping/active')) {
            $dataModel = $sellerOrders->getFirstItem();
            $multiShipMethod = $dataModel->getMultishipMethod();
            $method = explode('_', $multiShipMethod);
            $response = new \Magento\Framework\DataObject;
            $response->setCarrierCode($method[0]);
            $response->setMethod($method[1]);
            return $response;
        }
        return $order->getShippingMethod(true);    
    }
}
