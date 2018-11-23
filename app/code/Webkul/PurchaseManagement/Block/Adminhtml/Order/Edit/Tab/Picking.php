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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Order\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Picking extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Webkul\Accordionfaq\Model\ImagesFactory
     */
    protected $_supplieroptions;

    protected $_order;

    protected $_picking;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Webkul\Accordionfaq\Model\AddfaqFactory $faqFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Webkul\PurchaseManagement\Model\SupplieroptionsFactory $supplieroptions,
        \Webkul\PurchaseManagement\Model\OrderFactory $orderFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Webkul\PurchaseManagement\Model\PickingFactory $pickingFactory,
        array $data = []
    ) { 
        $this->_order= $orderFactory;
        $this->_supplieroptions = $supplieroptions;
        $this->_coreRegistry = $coreRegistry;
        $this->_picking=$pickingFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('purchasemanagement_order_picking');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_picking->create()->getCollection();
        $order_id = $this->getRequest()->getParam('id');
        if ($order_id) {
          $collection->addFieldToFilter('purchase_id', array('eq' => $order_id));
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header'    => __('Increment Id'),
            'align'     =>'left',
            'index'     => 'increment_id',
            ]
          );

        $this->addColumn('schedule_date',[
          'header'  =>__('Schedule Date'),
          'align'   =>'center',
          'index'   =>'schedule_date',
          'type'    =>'datetime'
        ]);

        $this->addColumn("source", [
              "header"    => __("Source Document"),
              "align"     =>"left",
              "index"     => "source",
        ]);

        $this->addColumn("supplier_id", [
              "header"    => __("Supplier Id"),
              "align"     =>"left",
              "index"     => "supplier_id",
        ]);

        $this->addColumn("status", [
              "header"    => __("Status"),
              "align"     =>"left",
              "index"     => "status",
        ]);

        $this->addColumn("action", [
            "header"    =>  __("Action"),
            "width"     => "100",
            "type"      => "action",
            "getter"    => "getEntityId",
            "actions"   => [
                [
                    "caption"   => __("View"),
                    "url"       => ["base"=> "*/picking/edit"],
                    "field"     => "id"
                ]
            ],
            "filter"    => false,
            "sortable"  => false
        ]);
        
      return parent::_prepareColumns();
    }


    /**
     * @return string
     */
    public function getRowUrl($row)
    {
        return "javascript:void(0)";
    }

    
}
