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

class View extends \Magento\Backend\Block\Template
{
    /**
    * @var \Magento\Store\Model\System\Store
    */
    protected $_systemStore;

    protected $_suppliers;

    protected $_order;

    protected $_orderitem;

    protected $_coreRegistry = null;

    private $history;

    /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\Framework\Data\FormFactory $formFactory
    * @param \Magento\Store\Model\System\Store $systemStore
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\PurchaseManagement\Model\OrderFactory $orderFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory $orderItemFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory $suppliersFactory,
        \Webkul\PurchaseManagement\Model\HistoryFactory $historyFactory,
        array $data = []
    ) {
        $this->_coreRegistry=$registry;
        $this->_order=$orderFactory;
        $this->_orderitem=$orderItemFactory;
        $this->_suppliers=$suppliersFactory;
        $this->history=$historyFactory;
        parent::__construct($context, $data);
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

    public function getQuotationData()
    {
        return $this->_coreRegistry->registry('purchasemanagement_data');
        if ($this->_coreRegistry->registry('purchasemanagement_data')->getId()) {
            return $this->_coreRegistry->registry('purchasemanagement_data');
        } else {
            return null;
        }
    }

    public function getOrderItemCollection()
    {
        if ($this->getRequest()->getParam('id')) {
            $orderItem=$this->_orderitem->create()->getCollection()
                        ->addFieldToFilter('purchase_id',$this->getRequest()->getParam('id'));
            return $orderItem;
        }
        else return [];
    }

    public function getSupplier($id)
    {
        return $this->_suppliers->create()->load($id);
    }

    public function getHistory($id)
    {
        return $this->history->create()->getCollection()->addFieldToFilter('parent_id',$id)
                    ->addFieldToFilter('entity_name',['in'=>['order','quotation']]);
    }
}
