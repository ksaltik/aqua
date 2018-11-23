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

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
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
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
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
        $this->setId('purchasemanagement_suppliers_products');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_supplieroptions->create()->getCollection();
        $supplier_id = $this->getRequest()->getParam('id');
        
        $collection->addFieldToFilter('supplier_id', array('eq' => $supplier_id));
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_id', [
            'header'    => __('Product Id'),
            'align'     =>'left',
            'index'     => 'product_id',
            ]
          );

        $this->addColumn('minimal_qty', [
            'header'    => __('Minimal Quantity'),
            'align'     =>'left',
            'index'     => 'minimal_qty',
        ]);

        $this->addColumn('lead_time', [
            'header'    => __('Lead Time(In Days)'),
            'align'     =>'left',
            'index'     => 'lead_time',
        ]);

        $this->addColumn('sequence', [
            'header'    => __('Priority'),
            'align'     =>'left',
            'index'     => 'sequence',
        ]);      

        $this->addColumn('created_at',[
          'header'  =>__('Created At'),
          'align'   =>'center',
          'index'   =>'created_at',
          'type'    =>'datetime'
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
