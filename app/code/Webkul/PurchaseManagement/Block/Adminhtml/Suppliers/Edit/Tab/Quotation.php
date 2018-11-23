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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Suppliers\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Quotation extends \Magento\Backend\Block\Widget\Grid\Extended
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
        array $data = []
    ) { 
        $this->_order= $orderFactory;
        $this->_supplieroptions = $supplieroptions;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('purchasemanagement_suppliers_quotation');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_order->create()->getCollection();
        $collection->addFieldToFilter("status", ["in"=>[0,1,4]]);
        $supplier_id = $this->getRequest()->getParam('id');
        if ($supplier_id) {
          $collection->addFieldToFilter('supplier_id', array('eq' => $supplier_id));
        }
              
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

  protected function _prepareColumns()
  {
      $this->addColumn('increment_id', [
          'header'    => __('Product Id'),
          'align'     =>'left',
          'index'     => 'increment_id',
          ]
        );
      $this->addColumn('created_at',[
        'header'  =>__('Created Date'),
        'align'   =>'center',
        'index'   =>'created_at',
        'type'    =>'datetime'
      ]);

      $this->addColumn("source", [
            "header"    => __("Source Document"),
            "align"     =>"left",
            "index"     => "source",
      ]);

      

      $this->addColumn("supplier_email", [
            "header"    => __("Supplier Email"),
            "align"     =>"left",
            "index"     => "supplier_email",
        ]);

        // $status = Mage::getModel("purchasemanagement/status")->getStatusArray();
        // $this->addColumn("status", array(
        //     "header"    => Mage::helper("purchasemanagement")->__("Status"),
        //     "align"     =>"left",
        //     "index"     => "status",
        //     "type"      => "options",
        //     "options"     => $status,
        // ));
        // 
        $this->addColumn("action", [
            "header"    =>  __("Action"),
            "width"     => "100",
            "type"      => "action",
            "getter"    => "getEntityId",
            "actions"   => [
                [
                    "caption"   => __("View"),
                    "url"       => ["base"=> "*/quotation/view"],
                    "field"     => "id"
                ]
            ],
            "filter"    => false,
            "sortable"  => false,
            "index"     => "stores",
            "is_system" => true,
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

    /**
     * @return array|null
     */
    public function getSupplierProducts()
    {
        $products = [];
        $pros = $this->_coreRegistry->registry('purchasemanagement_suppliers')->getSupplier_Id();
        $pros = explode(",", $pros);
        foreach ($pros as $pro) {
            $products[$pro] = ['position' => $pro];
        }
        return $products;
    }

}
