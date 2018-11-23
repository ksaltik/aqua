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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Quotation\Edit\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended
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
    protected $_suppliers;

    protected $_productloader;

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
        \Webkul\PurchaseManagement\Model\SuppliersFactory $suppliers,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        array $data = []
    ) {
        $this->_suppliers = $suppliers;
        $this->_coreRegistry = $coreRegistry;
        $this->_productloader=$_productloader;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('purchasemanagement_quotation');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {
          $collection = $this->_productloader->create()->getCollection();
          $collection->addAttributeToSelect('*')
                    ->addAttributeToFilter('type_id','simple');
          
          $this->setCollection($collection);
          return parent::_prepareCollection();
    }

    /**
    * Prepare form
    *
    * @return $this
    */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', [
          'header'    => __('Product Id'),
          'align'     =>'left',
          'index'     => 'entity_id',
          ]
        );

      $this->addColumn('name', [
          'header'    => __('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ]);
      
      $this->addColumn("sku", array(
                "header"    => __("SKU"),
                "width"     => "80",
                "index"     => "sku"
      ));
      $this->addColumn("cost_price", array(
                "header"    => __("Price"),
                "width"     => "80",
                "type"      => "price",
                "index"     => "cost_price"
      ));

      $this->addColumn("in_products", array(
          "header"    => __("Select"),
          "header_css_class" => "a-center",
          "type"      => "checkbox",
          "name"      => "in_products",
          // "values"    => $this->_getSelectedProducts(),
          "align"     => "center",
          "index"     => "entity_id",
          "sortable"  => false,
      ));

      $this->addColumn("qty", array(
          "filter"    => false,
          "sortable"  => false,
          "header"    => __("Qty To Add"),
          // "renderer"  => "adminhtml/sales_order_create_search_grid_renderer_qty",
          "name"      => "qty",
          "inline_css"=> "qty",
          "align"     => "center",
          "type"      => "input",
          "validate_class" => "validate-number",
          "index"     => "qty",
          "width"     => "1",
      ));
      return parent::_prepareColumns();
    }

    /**
    * Check permission for passed action
    *
    * @param string $resourceId
    * @return bool
    */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    // public function getGridUrl()
    // {
    //     return $this->getUrl('purchasemanagement/*/suppliergrid', ['_current' => true]);
    // }

    /**
     * @return string
     */
    // public function getRowUrl($row)
    // {
    //     return false;//$this->getUrl('purchasemanagement/*/suppliergrid', ['_current' => true]);
    //      // $row->getId();
    // }
    public function getRowId($row)
    {
        return $row->getId();
    }
}
