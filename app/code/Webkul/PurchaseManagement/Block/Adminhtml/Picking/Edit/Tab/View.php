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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Picking\Edit\Tab;

class View extends \Magento\Backend\Block\Template
{
    /**
    * @var \Magento\Store\Model\System\Store
    */
    protected $_systemStore;

    protected $_suppliers;

    protected $_order;

    protected $_orderitem;

    protected $_move;

    private $history;

    protected $_coreRegistry = null;

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
        \Webkul\PurchaseManagement\Model\SuppliersFactory $suppliersFactory,
        \Webkul\PurchaseManagement\Model\MoveFactory $moveFactory,
        \Webkul\PurchaseManagement\Model\HistoryFactory $historyFactory,
        array $data = []
    ) { 
        $this->_coreRegistry=$registry;
        $this->_suppliers=$suppliersFactory;
        $this->_move=$moveFactory;
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

    public function getPickingData()
    {
        return $this->_coreRegistry->registry('purchasemanagement_data');
        if ($this->_coreRegistry->registry('purchasemanagement_data')->getId()) {
            return $this->_coreRegistry->registry('purchasemanagement_data');
        } else {
            return null;
        }
    }


    public function getSupplier($id)
    {
        return $this->_suppliers->create()->load($id);
    }

    public function getMoveObj()
    {
        return $this->_move->create();
    }

    public function getCommentObj($id)
    {
        return $this->history->create()->getCollection()->addFieldToFilter('parent_id', $id)
                    ->addFieldToFilter('entity_name', ['in'=>['picking']]);
    }
}
