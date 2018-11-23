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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Quotation;

class Create extends \Magento\Backend\Block\Widget\Form\Container
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
        $this->_objectId = 'order_id';
        $this->_blockGroup = 'Webkul_PurchaseManagement';
        $this->_controller = 'adminhtml_quotation';
        parent::_construct();
        $quotation_id = $this->getRequest()->getParam("id");
        if ($quotation_id) {
            $this->buttonList->update('save', 'label', __('Save'));
        } else {
            $this->buttonList->update('save', 'label', __('Create Order'));
        }
        $this->buttonList->update("save", "style", "display:none;");
        $this->buttonList->update("save", "class", "save to_save");
        $this->buttonList->remove("delete");
        $this->buttonList->remove("reset");
    }

    /**
     * Retrieve text for header element depending on loaded gallery
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('purchasemanagement_quotation')->getId()) {
            $title = $this->_coreRegistry->registry('purchasemanagement_quotation')->getName();
            $title = $this->escapeHtml($title);
            return __("Edit Quotation '%'", $title);
        } else {
            return __('Create New Quotation');
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
