<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PurchaseManagement
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\PurchaseManagement\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Catalog\Model\Product;

/**
 * Webkul PurchaseManagement ProductSaveAfter Observer.
 */
class AfterPlaceOrder implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    protected $_supplieroptions;
    protected $_optionsvalue;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    protected $_request;

    protected $_product;

    protected $_coreSession;

    protected $_checkoutSession;

    protected $_quoteRepository;

    protected $_stockRegistery;

    protected $_order;

    protected $_orderitem;

    protected $_helper;
    protected $_storeManager;

    protected $_supplier;


    /**
     * @param \Magento\Framework\ObjectManagerInterface   $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CollectionFactory                           $collectionFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ProductFactory $productLoader,
        \Webkul\PurchaseManagement\Model\SupplieroptionsFactory $supplieroptions,
        \Webkul\PurchaseManagement\Model\OptionsvalueFactory    $optionsvalue,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\Session\SessionManager $session,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Webkul\PurchaseManagement\Model\OrderFactory    $order,
        \Webkul\PurchaseManagement\Model\OrderitemFactory    $orderitem,
        \Webkul\PurchaseManagement\Model\SuppliersFactory    $suppliers,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_optionsvalue=$optionsvalue;
        $this->_supplieroptions=$supplieroptions;
        $this->_date = $date;
        $this->_request=$request;
        $this->_product=$productLoader;
        $this->_checkoutSession=$checkoutSession;
        $this->_quoteRepository= $quoteRepository;
        $this->_coreSession=$session;
        $this->_stockRegistery=$stockRegistry;
        $this->_order=$order;
        $this->_orderitem=$orderitem;
        $this->_helper=$helper;
        $this->_supplier=$suppliers;
        $this->_storeManager=$storeManager;
    }

    /**
     * Product delete after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $isMultiShipping = $this->_checkoutSession->getQuote()->getIsMultiShipping();
        if (!$isMultiShipping) {
            /** @var $orderInstance Order */
            $order = $observer->getOrder();
            $lastOrderId = $observer->getOrder()->getId();
            $this->checkForPurchaseOrder($order, $lastOrderId);
        } else {
            $quoteId = $this->_checkoutSession->getLastQuoteId();
            $quote = $this->_quoteRepository->get($quoteId);
            if ($quote->getIsMultiShipping() == 1 || $isMultiShipping == 1) {
                $orderIds = $this->_coreSession->getOrderIds();
                foreach ($orderIds as $ids => $orderIncId) {
                    $lastOrderId = $ids;
                    /** @var $orderInstance Order */
                    $order = $this->_orderRepository->get($lastOrderId);
                    $this->checkForPurchaseOrder($order, $lastOrderId);
                }
            }
        }
    }

    /*Method to Check order items is applicable for Purchase order generation*/
    public function checkForPurchaseOrder($order, $orderId)
    {
        $This_order = $order;
        $items = $This_order->getAllItems();
        foreach ($items as $item) {
            $product_id = $item->getProductId();
            $product = $this->_product->create()->load($product_id);
            $stock_data = $this->_stockRegistery->getStockItem($product_id);
            $mainqty = $stock_data->getQty();
            $procurement = $product->getProcurementMethod();
            if ($procurement==0) {
                if($this->_helper->getProcurementMethodConfig()==2)
                    $procurement=1;
            }
            if ($mainqty <=0 && $procurement == 1) {
                $product_options = '';
                /* Searching custom options for current item*/
                // $options = $item->getProductOptions();
                // if (isset($options['options'])) {
                //     $product_options = serialize($options['options']);
                // }
                $item_type = $item->getProductType();
                if ($item_type === 'configurable') {
                    continue;
                }
                
                $ordered_qty = $item->getQtyOrdered();
                /* Going to generate quotaion purchase order for current order*/
                $this->generatePurchaseQuotation($This_order, $product, $ordered_qty, $product_options);
            }
        }
    }

    public function generatePurchaseQuotation($This_order, $product, $ordered_qty, $product_options)
    {
        $order_id = $This_order->getId();
        $product_id = $product->getId();
        $weight = $product->getWeight();
        $description = $product->getName();
        $cost_price = $product->getCostPrice();
        $product_sku = $product->getSku();
        $source = $This_order->getIncrementId();
        $currency_code = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
        $order_model = $this->_order->create();
        /* Fetching supplier details from product supplier options*/ 
        $quote = $this->findProductCostPriceAndQty($product_id, $ordered_qty); 
        $supplier_id = $quote['supplier_id'];
        if (!$supplier_id)
            return false;
        $quantity = $quote['quantity'];
        if ($quote['cost_price'] > 0)
            $cost_price = $quote['cost_price'];
        $sub_total = $quantity*$cost_price;
        /*Order Model Set and Save */
        $order_model->setOrderId($order_id);
        $order_model->setSource($source);
        $order_model->setSourceType('order');
        $order_model->setWeight($weight);
        $order_model->setBaseSubtotal($sub_total);
        $order_model->setGrandTotal($sub_total);
        $order_model->setGlobalCurrencyCode($currency_code);
        $order_model->setTotalItemCount($quantity);
        $order_model->setCreatedAt($this->_date->gmtDate());
        if ($supplier_id) {
            $order_model->setSupplierId($supplier_id);
            $_supplier = $this->_supplier->create()->load($supplier_id);
            $order_model->setSupplierEmail($_supplier->getEmail());
        }
        $order_model->save();
        $quotation_id = $order_model->getId();
        $increment_id = $this->_helper->getIncrementNumber($quotation_id);
        $order_model->setIncrementId($increment_id);
        $order_model->save();
        /* Adding Item to purchase order*/
        $item_model = $this->_orderitem->create();
        $item_model->setPurchaseId($quotation_id);
        $item_model->setProductId($product_id);
        $item_model->setSku($product_sku);
        $item_model->setDescription($description);
        $item_model->setCustomOptions($product_options);
        $item_model->setQuantity($quantity);
        $item_model->setBasePrice($cost_price);
        $item_model->setWeight($weight);
        $item_model->setSubtotal($sub_total);
        $item_model->setCurrency($currency_code);
        $item_model->Save();
        return true;
    }
    
    public function findProductCostPriceAndQty($product_id, $qty){
        $cost_price = 0.0;
        $supplier_id = 0;
        $model = $this->_supplieroptions->create();
        $option_data = $model->getCollection()->addFieldToFilter("product_id",$product_id)
                                                ->addFieldToFilter("status",'1')
                                                ->setOrder('sequence','ASC')
                                                ->getData();

        if (count($option_data)>0) { 
            $option_id = $option_data[0]['entity_id'];
            $minimal_qty = $option_data[0]['minimal_qty'];
            $supplier_id = $option_data[0]['supplier_id'];
            if ($minimal_qty > $qty)
                $qty = $minimal_qty;

            $value_data = $this->_optionsvalue->create()->getCollection()->addFieldToFilter("entity_supplier_id",$option_id)->getData();
            foreach ($value_data as $value) {
                if ($value['quantity'] <= $qty) {
                    $cost_price = $value['base_price'];
                }
            }
        }
        return array('supplier_id'=>$supplier_id,'quantity'=>$qty, 'cost_price'=>$cost_price);
    }
   
}