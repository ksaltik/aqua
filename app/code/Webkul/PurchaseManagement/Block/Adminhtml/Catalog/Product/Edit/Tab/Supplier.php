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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Catalog\Product\Edit\Tab;
 
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
 
class Supplier extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'product/edit/supplier_tab.phtml';

    protected $_suppliers;

    protected $_supplieroptions;

    protected $_optionsvalue;
 
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
 
    public function __construct(
        Context $context,
        Registry $registry,
        \Webkul\PurchaseManagement\Model\SuppliersFactory $suppliersFactory,
        \Webkul\PurchaseManagement\Model\OptionsvalueFactory $optionsvalueFactory,
        \Webkul\PurchaseManagement\Model\SupplieroptionsFactory $supplieroptionsFactory,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->_suppliers=$suppliersFactory;
        $this->_optionsvalue=$optionsvalueFactory;
        $this->_supplieroptions=$supplieroptionsFactory;
        parent::__construct($context, $data);
    }
 
    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getSuppliers(){
        $suppliers=$this->_suppliers->create()->getCollection();
        return $suppliers;
    }

    public function getOptions(){
        $product_id=$this->getProduct()->getId();
        $options= $this->_supplieroptions->create()->getCollection()
                ->addFieldToFilter('product_id',$product_id);
        return $options;
    }

    public function getOptionsvalue($supplier_id){
        $value=$this->_optionsvalue->create()->getCollection()
                ->addFieldToFilter('entity_supplier_id',$supplier_id);
        return $value;
    }
    
}