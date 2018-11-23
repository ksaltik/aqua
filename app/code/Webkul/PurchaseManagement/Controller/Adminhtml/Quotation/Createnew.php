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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Quotation;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Createnew extends Action
{
    protected $_suppliers;

    protected $_order;

    protected $_orderitem;

    protected $_productloader;

    protected $_storeManager;

    protected $_helper;

    protected $_loginUser;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory  $suppliersFactory,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderitemFactory,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Webkul\PurchaseManagement\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_suppliers = $suppliersFactory;
        $this->_order=$orderFactory;
        $this->_orderitem=$orderitemFactory;
        $this->_productloader=$productloader;
        $this->_storeManager=$storeManager;
        $this->_helper=$helper;
        $this->_date = $date;
        $this->_loginUser = $context->getAuth()->getUser();
    }

    public function execute()
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            $params=$this->getRequest()->getParams();

            $weight = 0.0;
            $grand_total = 0.0;
            $item_count = 0;
            $source = 'MANUAL';
            $quotation_id = 0;
            $quotation_edit_id = 0;
            if (isset($params['id'])) {
                $quotation_edit_id = $params['id'];
            }
            $params['source'] = strip_tags($params['source']);
            $purchase_params = $params['purchase'];
            $quantity_params = $purchase_params['qty'];
            $currency_code = $this->_storeManager->getStore()->getBaseCurrency()->getCode();
            if ($quotation_edit_id) {
                
                $order_model  = $this->_order->create()->load($quotation_edit_id);
                $weight = $order_model->getWeight();
                $item_count = $order_model->getTotalItemCount();
                $grand_total = $order_model->getGrandTotal();

                $source = $params['source'];
                $supplier_id = $params['supplier_id'];
                $order_model->setSource($source);
                $order_model->setSupplierId($supplier_id);
                $order_model->save();
            } else {
                
                if ($params['source']) {
                    $source = $params['source'];
                }
                $supplier_id = $params['supplier_id'];
                $order_model = $this->_order->create();
                if ($supplier_id) {
                    $order_model->setSupplierId($supplier_id);
                    $_supplier = $this->_suppliers->create()->load($supplier_id);
                    $order_model->setSupplierEmail($_supplier->getEmail());
                }
                $order_model->setSource($source);
                $order_model->setSourceType('manual');
                $order_model->setGlobalCurrencyCode($currency_code);
                $order_model->setCreatedAt($this->_date->gmtDate());

                //if( !empty($this->getRequest()->getParam('admin_id')) && $this->getRequest()->getParam('admin_id') != "" ){
                if( !empty($this->getRequest()->getParam('website_id')) && $this->getRequest()->getParam('website_id') != "" ){
                    $order_model->setWebsiteId($this->getRequest()->getParam('website_id'));
                } 
                
                $order_model->save();
                $quotation_id = $order_model->getId();
                $increment_id = $this->_helper->getIncrementNumber($quotation_id);
            }
            foreach ($purchase_params['product'] as $index => $product_id) {
                $quantity = 0;
                $sub_total = 0.0;
                $item_model = $this->_orderitem->create();
                $_product = $this->_productloader->create()->load($product_id);
                $description = $_product->getName();
                $weight = $weight + $_product->getWeight();
                if (isset($quantity_params[$index])) {
                    $quantity = $quantity_params[$index];
                    $sub_total = $_product->getCostPrice()*$quantity;
                    $item_count = $item_count + $quantity;
                }
                $grand_total = $grand_total + $sub_total;
                if ($quotation_edit_id) {
                    $item_model->setPurchaseId($quotation_edit_id);
                } else {
                    $item_model->setPurchaseId($quotation_id);
                }
                $item_model->setProductId($product_id);
                $item_model->setSku($_product->getSku());
                $item_model->setDescription($description);
                $item_model->setQuantity($quantity);
                $item_model->setBasePrice($_product->getCostPrice());
                $item_model->setWeight($_product->getWeight());
                $item_model->setSubtotal($sub_total);
                if (isset($params["options"][$product_id])) {
                    $options_array = $params["options"][$product_id];
                    // $options_data = Mage::getModel("purchasemanagement/optiontitle")->getOptionsArrayForItem($options_array, $product_id, $_product->getStoreId());
                    // $item_model->setCustomOptions(serialize($options_data));
                }
                $item_model->setCurrency($currency_code);
                $item_model->setCreatedAt($this->_date->gmtDate());
                $item_model->Save();
            }
            $order_model->setWeight($weight);
            $order_model->setTotalItemCount($item_count);
            $order_model->setBaseSubtotal($grand_total);
            $order_model->setGrandTotal($grand_total);
            if (!$quotation_edit_id) {
                $order_model->setIncrementId($increment_id);
            } else {
                $order_model->setId($quotation_edit_id);
            }
            $order_model->save();
            /* finally assign quotation id*/
            if ($quotation_edit_id) {
                $quotation_id = $quotation_edit_id;
            }
            /* Purchase order comment exists after*/
            if (isset($params['comment'])) {
                $comment = $params['comment'];
                if ($params['is_supplier_notified']) {
                    $notify = $params['is_supplier_notified'];
                }
                $this->_helper->addHistory($comment, 'order', $quotation_id, $notify);
            }
            $this->messageManager->addSuccess(__('Quotation saved succesfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        

        return $resultRedirect->setPath('*/*/');
    }
}
