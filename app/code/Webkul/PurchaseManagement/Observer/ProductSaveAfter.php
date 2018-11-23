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
class ProductSaveAfter implements ObserverInterface
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
        \Webkul\PurchaseManagement\Model\OptionsvalueFactory    $optionsvalue
    ) {
        $this->_objectManager = $objectManager;
        $this->_optionsvalue=$optionsvalue;
        $this->_supplieroptions=$supplieroptions;
        $this->_date = $date;
        $this->_request=$request;
        $this->_product=$productLoader;
    }

    /**
     * Product delete after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            // echo "<pre>"; print_r($this->_request->getParams()); die;
            $productId = $observer->getProduct()->getId();
            $supplier_arr = $this->_request->getParam("supplierarr");
            $data = $this->_request->getParam("supplier");
            $todelete = $this->_request->getParam("todelete");
            $todeletesupplier = $this->_request->getParam("todeletesupplier");
            $pro_id = $this->_request->getParam("id");
            $product=$this->_product->create()->load($productId);
            $product_type = $product->getTypeID();
            if ($todelete) {
                foreach ($todelete as $option_value_id) {
                    $this->_optionsvalue->create()->load($option_value_id)->delete();
                }
            }
            if ($todeletesupplier) {
                foreach ($todeletesupplier as $option_id) {
                    $this->removeAllOptions($option_id);
                    $options=$this->_supplieroptions->create()->getCollection()->addFieldToFilter('entity_id',$option_id);
                    foreach ($options as $key) {
                        $key->setId($key->getEntityId())->delete();
                    }
                }
            }
            if (isset($supplier_arr['supplier'])) {
                foreach ($supplier_arr['supplier'] as $supplier_key => $supplier_id) {
                    /* checking configurable product and*/
                    $this->updateOptionsInProduct($data, $pro_id, $supplier_key, $supplier_id, $supplier_arr);
                    // if ($product_type == "configurable"){
                    //     $child_ids = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($pro_id);
                    //     foreach ($child_ids[0] as $child_id) {
                    //         $this->updateOptionsInProduct($data, $child_id, $supplier_key, $supplier_id, $supplier_arr);
                    //     }
                    // }
                }
            }
            // echo "<pre>";
            // print_r($data); die;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }

    function updateOptionsInProduct($data, $pro_id, $supplier_key, $supplier_id, $supplier_array) {
        $model = $this->_supplieroptions->create();
        $status = $supplier_array['status'][$supplier_key];
        $minimal_qty = $supplier_array['minimal'][$supplier_key];
        $lead_time = $supplier_array['lead_time'][$supplier_key];
        $sequence = $supplier_array['sequence'][$supplier_key];
        $existing = $model->getCollection()->addFieldToFilter("product_id",$pro_id)->addFieldToFilter("supplier_id",$supplier_id)->getData();
        if (count($existing) == 0) {
            $model->setProductId($pro_id);
            $model->setStatus($status);
            $model->setMinimalQty($minimal_qty);
            $model->setLeadTime($lead_time);
            $model->setSequence($sequence);
            $model->setSupplierId($supplier_id);
            $entity_supplier_id = $model->save()->getId();
        } else {
            $entity_supplier_id = $existing[0]['entity_id'];
            $model->load($entity_supplier_id);
            $model->setStatus($status);
            $model->setMinimalQty($minimal_qty);
            $model->setLeadTime($lead_time);
            $model->setSequence($sequence);
            $model->setSupplierId($supplier_id);
            $model->setId($entity_supplier_id);
            $model->save();
        }
        if ($entity_supplier_id) {
            $this->removeAllOptions($entity_supplier_id);
            if (isset($data[$supplier_key]) && isset($data[$supplier_key]['price'])) {
                foreach ($data[$supplier_key]["price"] as $key => $value1) {
                    $price = $data[$supplier_key]["price"][$key];
                    $qty = $data[$supplier_key]["qty"][$key];
                    $value_model = $this->_optionsvalue->create();
                    $value_model->setEntitySupplierId($entity_supplier_id);
                    $value_model->setBasePrice($price);
                    $value_model->setQuantity($qty);
                    $value_model->save();
                }
            }
        }
    }

    function removeAllOptions($option_id) {
        $collection1 = $this->_optionsvalue->create()->getCollection()
                                                                    ->addFieldToFilter("entity_supplier_id",$option_id);
        foreach ($collection1 as $col) {
            // $this->_optionsvalue->create()->load($col->getEntityId())->delete();
            $col->setId($col->getEntityId())->delete();
        }
        return true;
    }
}
