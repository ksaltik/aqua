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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Order;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_suppliers;

    protected $_order;

    protected $_orderitem;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Webkul\PurchaseManagement\Model\OrderFactory $orderFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory $orderItemFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory $suppliersFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_order=$orderFactory;
        $this->_orderitem=$orderItemFactory;
        $this->_suppliers=$suppliersFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialize imagegallery gallery edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_blockGroup = 'Webkul_PurchaseManagement';
        $this->_controller = 'adminhtml_order';
        parent::_construct();
               
        $this->buttonList->add('rfq', array(
            'label'   => __('Send by Email'),
            'onclick' => "setLocation('{$this->getUrl('*/*/orderrfqemail',array('id'=>$this->getRequest()->getParam("id")))}')",
            'class'   => 'save'
        ));
        
        $this->buttonList->add('confirm', array(
            'label'   => __('Print Order'),
            'onclick' => "setLocation('{$this->getUrl('*/*/printorder',array('id'=>$this->getRequest()->getParam("id")))}')",
            'class'   => 'save'
        ));
        $this->buttonList->remove("save");
        $this->buttonList->remove("reset");
        $this->buttonList->remove("delete");
    }

    /**
     * Retrieve text for header element depending on loaded gallery
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('purchasemanagement_data')->getId()) {
            $title = $this->_coreRegistry->registry('purchasemanagement_data')->getId();
            $title = $this->escapeHtml($title);
            return __("Order '%'", $title);
        } else {
            return __('Create New Order');
        }
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
}
