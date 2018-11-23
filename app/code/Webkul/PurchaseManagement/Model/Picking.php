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
namespace Webkul\PurchaseManagement\Model;

use Webkul\PurchaseManagement\Api\Data\PickingInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;

class Picking extends \Magento\Framework\Model\AbstractModel implements PickingInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**#@+
     * Picking Status
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * PurchaseManagement Picking cache tag
     */
    const CACHE_TAG = 'purchasemanagement_picking';

    /**
     * @var string
     */
    protected $_cacheTag = 'purchasemanagement_picking';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchasemanagement_picking';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\PurchaseManagement\Model\ResourceModel\Picking');
    }

    protected $_order;

    protected $_orderitem;

    protected $_options;

    protected $_picking;

    protected $_move;

    protected $_helper;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry  $registry,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderItemFactory,
        \Webkul\PurchaseManagement\Model\SupplieroptionsFactory  $supplierOptionsFactory,
        \Webkul\PurchaseManagement\Model\PickingFactory  $pickingFactory,
        \Webkul\PurchaseManagement\Model\MoveFactory  $moveFactory,
        \Webkul\PurchaseManagement\Helper\Data $helper
    ) {
        parent::__construct($context,$registry);
        $this->_move=$moveFactory;
        $this->_order=$orderFactory;
        $this->_orderitem=$orderItemFactory;
        $this->_options=$supplierOptionsFactory;
        $this->_picking=$pickingFactory;
        $this->_helper=$helper;
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteFaq();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Images
     *
     * @return \Webkul\PurchaseManagement\Model\Picking
     */
    public function noRouteFaq()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

  
    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\PurchaseManagement\Api\Data\PickingInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    public function calculateScheduleDate($Purchase_order, $product_id = 0)
    {
        $lead_time = 0;
        $schedule_date = date('Y-m-d');
        $order_id = $Purchase_order->getEntityId();
        $supplier_id = $Purchase_order->getSupplierId();
        
        if ($product_id) {
            $lead_time = $this->fetchSupplierLeadTime($supplier_id, $product_id);
        } else {
            $item_lead = 0;
            $item_count = 0;
            $item_collection = $this->_orderitem->create()->getCollection()
                                                ->addFieldToFilter("purchase_id",$order_id);
            foreach ($item_collection as $item) {
                $product_id = $item->getProductId();
                $item_lead = $item_lead + $this->fetchSupplierLeadTime($supplier_id, $product_id);
                $item_count = $item_count++;
            }
            if ($item_count)
                $lead_time = (integer)$item_lead/$item_count;
        }
        if ($lead_time) {
            $date = new \DateTime('+'.$lead_time.' day');
            $schedule_date = $date->format('Y-m-d H:i:s');  
        }
        return $schedule_date;
    }

    public function triggerPicking($Purchase_order, $picking_status = 1)
    {
        $order_id = $Purchase_order->getEntityId();
        $source = $Purchase_order->getIncrementId();
        $supplier_id = $Purchase_order->getSupplierId();
        $supplier_email = $Purchase_order->getSupplierEmail();
        $total_weight = $Purchase_order->getWeight();
        $total_item = $Purchase_order->getTotalItemCount();
        $schedule_date = $this->calculateScheduleDate($Purchase_order);
        /*generating Picking after order confirm*/
        //$picking_status = 1; // ready to transfer state
        $picking_model = $this->_picking->create();
        $picking_model->setSource($source);
        $picking_model->setPurchaseId($order_id);
        $picking_model->setSupplierId($supplier_id);
        $picking_model->setSupplierEmail($supplier_email);
        $picking_model->setTotalItemCount($total_item);
        $picking_model->setWeight($total_weight);
        $picking_model->setStatus($picking_status);
        $picking_model->setScheduleDate($schedule_date);
        $picking_model->save();
        $picking_id = $picking_model->getId();
        $picking_increment = "IN/".str_pad($picking_id, 6, '0', STR_PAD_LEFT);
        $picking_model->setIncrementId($picking_increment);
        $picking_model->save();
        $item_collection = $this->_orderitem->create()->getCollection()
                                                    ->addFieldToFilter("purchase_id",$order_id);
        foreach ($item_collection as $item) {
            $product_id = $item->getProductId();
            $qty = $item->getQuantity() - $item->getReceivedQty();
            $schedule_date = $this->calculateScheduleDate($Purchase_order, $product_id);
            if ($qty > 0) {
                $move_model = $this->_move->create();
                $move_model->setPickingId($picking_id);
                $move_model->setSource($source);
                $move_model->setProductId($product_id);
                $move_model->setSku($item->getSku());
                $move_model->setCustomOptions($item->getCustomOptions());
                $move_model->setDescription($item->getDescription());
                $move_model->setWeight($item->getWeight());
                $move_model->setQuantity($qty);
                $move_model->setStatus($picking_status);
                $move_model->setScheduleDate($schedule_date);
                $move_model->save();
                if ($picking_status == 2) {
                    $this->_helper->update_product_inventory($product_id, $qty);
                }
            }
        }
        return $picking_id;
    }

    public function fetchSupplierLeadTime($supplier_id, $product_id)
    {
        $lead_time = 0;
        $options_collection = $this->_options->create()->getCollection()
                                                        ->addFieldToFilter("supplier_id",$supplier_id)
                                                        ->addFieldToFilter("product_id",$product_id);
        foreach ($options_collection as $options) {
            $lead_time = $options->getLeadTime();
        }
        return $lead_time;
    }
}
