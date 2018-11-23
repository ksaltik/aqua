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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Suppliers;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize imagegallery gallery edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'supplier_id';
        $this->_blockGroup = 'Webkul_PurchaseManagement';
        $this->_controller = 'adminhtml_suppliers';
        parent::_construct();
        if ($this->_isAllowedAction('Webkul_PurchaseManagement::suppliers')) {
            $this->buttonList->update('save', 'label', __('Save Supplier'));
        } else {
            $this->buttonList->remove('save');
        }
    }

    /**
     * Retrieve text for header element depending on loaded gallery
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('purchasemanagement_suppliers')->getId()) {
            $title = $this->_coreRegistry->registry('purchasemanagement_suppliers')->getName();
            $title = $this->escapeHtml($title);
            return __("Edit Supplier '%'", $title);
        } else {
            return __('New Supplier');
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
