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
namespace Webkul\PurchaseManagement\Helper;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\TestFramework\ErrorLog\Logger;

/**
 * Webkul PurchaseManagement Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   
    protected $_storeManager;

    protected $_order;

    protected $_product;

    protected $_history;

    private $inlineTranslation;

    private $transportBuilder;

    private $_supplier;
    
    private $_picking;

    private $messageManager;

    private $_move;
    /**
     * @var Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockStateInterface;
 
    /**
     * @var Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    protected $_countryCollectionFactory;

    protected $_localeCurrency;

    /**
     * Undocumented function
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Webkul\PurchaseManagement\Model\OrderFactory $orderFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $product
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Webkul\PurchaseManagement\Model\HistoryFactory $historyFactory
     * @param \Webkul\PurchaseManagement\Model\SuppliersFactory $supplierFactory
     * @param \Webkul\PurchaseManagement\Model\PickingFactory $pickingFactory
     * @param \Webkul\PurchaseManagement\Model\MoveFactory $moveFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\PurchaseManagement\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $product,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Webkul\PurchaseManagement\Model\HistoryFactory  $historyFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory  $supplierFactory,
        \Webkul\PurchaseManagement\Model\PickingFactory  $pickingFactory,
        \Webkul\PurchaseManagement\Model\MoveFactory  $moveFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency
    ) {
        $this->_storeManager=$storeManager;
        $this->_order=$orderFactory;
        $this->_product = $product;
        $this->_stockStateInterface = $stockStateInterface;
        $this->_stockRegistry = $stockRegistry;
        $this->_history=$historyFactory;
        $this->inlineTranslation=$inlineTranslation;
        $this->transportBuilder=$transportBuilder;
        $this->_supplier=$supplierFactory;
        $this->_picking=$pickingFactory;
        $this->_move=$moveFactory;
        $this->_countryCollectionFactory = $countryFactory;
        $this->messageManager = $messageManager;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context);
    }
    public function getIncrementNumber($quotation_id)
    {
        $prefix = $this->scopeConfig->getValue(
            'purchasemanagement/general_options/prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $padding = $this->scopeConfig->getValue(
            'purchasemanagement/general_options/padding',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($padding) {
            $increment_id = str_pad($quotation_id, $padding, '0', STR_PAD_LEFT);
            if ($prefix) {
                $increment_id = $prefix.$increment_id;
            } else {
                $increment_id = '1'.$increment_id;
            }
        } else {
            $increment_id = $quotation_id;
        }
        return $increment_id;
    }

    public function getOrder($id)
    {
        return $this->_order->create()->load($id);
    }

    public function getProcurementMethodConfig()
    {
        return $this->scopeConfig->getValue(
            'purchasemanagement/general_options/procurement_method',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * For Update stock of product
     * @param int $productId which stock you want to update
     * @param array $stockData your updated data
     * @return void
    */
    public function updateProductStock($productId, $receive_qty)
    {
        $product=$this->_product->getById($productId); //load product which you want to update stock
        $sku=$product->getSku();
        $stockItem=$this->_stockRegistry->getStockItem($productId); // load stock of that product
        $oldStock= $stockItem->getQty();
        $total_quantity=$receive_qty+$oldStock;
        $stockItem->setQty($total_quantity);
        $this->_stockRegistry->updateStockItemBySku($sku,$stockItem);
        $product->get($sku, false, $product->getStoreId(), true);
    }

    public function addHistory($comment, $objective, $order_id, $is_notified=0, $status_id)
    {
        $comments=$this->_history->create();
        $comments->setParentId($order_id)
                ->setComment($comment)
                ->setEntityName($objective)
                ->setIsSupplierNotified($is_notified)
                ->setStatus($status_id)
                ->save();
        if ($is_notified) {
            $this->notifyToSupplier($objective, $order_id, $status_id, $comment);
        }
        return true;
    }

    public function notifyToSupplier($objective, $parent_id, $status, $comment)
    {
        $supplier_id = 0;
        $temp_vars = array();
        $adminEmail=$this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $from = ['email' => $adminEmail, 'name' => 'Admin'];
        $temp_vars["status"] = $status;
        $temp_vars["message"] = $comment;
        switch ($objective) {
            case 'quotation':
                $order = $this->_order->create()->load($parent_id);
                $temp_vars["object"] = "Quotation";
                $temp_vars["orderId"] = $order->getIncrementId();
                $supplier_id = $order->getSupplierId();
                break;
            case 'order':
                $order = $this->_order->create()->load($parent_id);
                $temp_vars["object"] = "Purchase Order";
                $temp_vars["orderId"] = $order->getIncrementId();
                $supplier_id = $order->getSupplierId();
                break;
            case 'picking':
                $picking = $this->_picking->create()->load($parent_id);
                $temp_vars["object"] = "Incoming Shipment";
                $temp_vars["orderId"] = $picking->getIncrementId();
                $supplier_id = $picking->getSupplierId();
                break;
            case 'move':
                $move = $this->_move->create()->load($parent_id);
                $temp_vars["object"] = "Incoming Product";
                $picking_id = $move->getPickingId();
                $picking = $this->_picking->create()->load($picking_id);
                $temp_vars["orderId"] = $move->getSource()."(".$move->getDescription().")";
                $supplier_id = $picking->getSupplierId();
                break;
        }
        if ($supplier_id) {
            $supplier = $this->_supplier->create()->load($supplier_id);
            $supplier_name = $supplier->getName();
            $supplier_email = $supplier->getEmail();
            $temp_vars["name"] = $supplier_name;

            $templateOptions=['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId()];
            try{
                $this->inlineTranslation->suspend();
                $transport = $this->transportBuilder->setTemplateIdentifier('purchasemanagement_comment_email')
                          ->setTemplateOptions($templateOptions)
                          ->setTemplateVars($temp_vars)
                          ->setFrom($from)
                          ->addTo($supplier_email)
                          ->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
                return true;
            }catch(\Exception $e){
                $this->messageManager->addError($e->getMessage());
            }
        }
        return false;
    }
    
    public function sendRfqEmail($purchase_id)
    {
        $temp_vars = [];
        
        $adminEmail=$this->scopeConfig->getValue(
            'trans_email/ident_general/email', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $order = $this->_order->create()->load($purchase_id);
        $supplier_email = $order->getSupplierEmail();
        $supplier = $this->_supplier->create()->load($order->getSupplierId());
        $supplier_name = $supplier->getName();
        $temp_vars["name"] = $supplier_name;
        $temp_vars["order"] = $order;
        $status = $order->getStatus();
        if ($status <= 1) {
            $temp_vars["order_type"] = "Quotation";
            $template = $this->scopeConfig->getValue(
                'purchasemanagement/email/quotation_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            $temp_vars["order_type"] = "Purchase Order";
            $template = $this->scopeConfig->getValue(
                'purchasemanagement/email/order_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        
        $from = ['email' => $adminEmail, 'name' => 'Admin'];
        $templateOptions = ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->_storeManager->getStore()->getId()];
        try{
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder->setTemplateIdentifier($template)
                      ->setTemplateOptions($templateOptions)
                      ->setTemplateVars($temp_vars)
                      ->setFrom($from)
                      ->addTo($supplier_email)
                      ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        
    }

    public function cancelRelatedPicking($order_id)
    {
        $picking_model = $this->_picking->create();
        $picking_collection = $picking_model->getCollection()
                                        ->addFieldToFilter("purchase_id",$order_id)
                                        ->addFieldToFilter("status",2);
        foreach ($picking_collection as $picking) {
            $picking_id = $picking->getEntityId();
            $item_collection = $this->_move->create()->getCollection()
                                            ->addFieldToFilter("picking_id",$picking_id);
            foreach ($item_collection as $item) {
                $status = $item->getStatus();
                if ($status == 2) {
                    $product_id = $item->getProductId();
                    $quantity = -($item->getQuantity());
                }
                $item->setStatus(3);
                $item->setId($item->getEntityId());
                $item->save();
            }
            $picking->setStatus(3);
            $picking->setId($picking->getEntityId());
            $picking->save();
        }
        return true;
    }

    /* Two Argument
        picking id and 
        picking status id
    */
    public function changePickingStatus($picking_id, $status_id){
        $picking = $this->_picking->create()->load($picking_id);
        $picking->setStatus($status_id);
        $picking->setId($picking_id);
        $picking->save();
        $moves = $this->_move->create()->getCollection()->addFieldToFilter("picking_id",$picking_id);
        foreach ($moves as $move) {
            $mv = $this->_move->create()->load($move->getEntityId());
            $mv->setStatus($status_id)->setId($move->getEntityId());
            $mv->save();
            if ($status_id == 2) {
                $product_id = $mv->getProductId();
                $quantity = $mv->getQuantity();
                $this->updateProductStock($product_id, $quantity);
            }
        }
        return true;
    }

    /**
     * getCountryList function
     *  get all countries with code
     * @return country array
     */
    public function getCountryList() {
        $collection = $this->_countryCollectionFactory->create()
                    ->getCollection()->toOptionArray();
        return $collection;
    }

    /**
     * getCountryname function
     *
     * @param [string] $countryCode
     * @return string country name
     */
    public function getCountryname($countryCode) {
        $country = $this->_countryCollectionFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * format price in currency code, no conversion
     *
     * @param [float] $amount
     * @param string $currencyCode
     * @return void
     */
    public function getFormatPrice($amount, $currencyCode = '') {
        $symbol = $this->_localeCurrency->getCurrency($currencyCode)->getSymbol();
        return '<span class="price">'.$symbol.$amount.'</span>';
    }
}