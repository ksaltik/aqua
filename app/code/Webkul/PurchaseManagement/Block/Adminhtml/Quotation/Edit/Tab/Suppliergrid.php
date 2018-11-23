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

class Suppliergrid extends \Magento\Backend\Block\Widget\Grid\Extended
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
        array $data = []
    ) {
        $this->_suppliers = $suppliers;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('purchasemanagement_quotation_supplier');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }
    protected function _prepareCollection()
    {
          $collection = $this->_suppliers->create()->getCollection();
          // $supplier_id = $this->getRequest()->getParam('id');
          // $collection->addFieldToFilter('supplier_id', array('eq' => $supplier_id));
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
        $this->addColumn('id', [
          'header'    => __('Supplier Id'),
          'align'     =>'left',
          'index'     => 'id',
          ]
        );

      $this->addColumn('name', [
          'header'    => __('Name'),
          'align'     =>'left',
          'index'     => 'name',
      ]);

      $this->addColumn('email', [
          'header'    => __('Email'),
          'align'     =>'left',
          'index'     => 'email',
      ]);

      $this->addColumn('company', [
          'header'    => __('Company'),
          'align'     =>'left',
          'index'     => 'company',
      ]);      

      $this->addColumn('phone',[
        'header'  =>__('Telephone'),
        'align'   =>'center',
        'index'   =>'phone',
      ]);
      $this->addColumn('zip',[
        'header'  =>__('Zip'),
        'align'   =>'center',
        'index'   =>'zip',
      ]);

      // $this->addColumn('Select',[
      //   'header'  =>__('Action'),
      //   'align'   =>'center',
      //   'index'   => 'id'
      // ]);
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

    public function getGridUrl()
    {
        return $this->getUrl('purchasemanagement/*/suppliergrid', ['_current' => true]);
    }

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
