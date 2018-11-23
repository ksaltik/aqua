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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Picking;

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
        \Webkul\PurchaseManagement\Model\Picking $pickingModel,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_pickingModel = $pickingModel;
        parent::__construct($context, $data);
    }

    /**
     * Initialize imagegallery gallery edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $pickingId = $this->getRequest()->getParam('id');
        $data = $this->_pickingModel->load($pickingId);
        $this->_objectId = 'picking_id';
        $this->_blockGroup = 'Webkul_PurchaseManagement';
        $this->_controller = 'adminhtml_picking';
        parent::_construct();
        $shipment_id = $this->getRequest()->getParam("id");

        if ($data->getStatus() != 2) {
            $this->buttonList->add('partial', array(
                'label'   => __('Partial Shipment'),
                'class'   => 'save'
            ));
        }
        
        $this->buttonList->remove("save");
        $this->buttonList->remove("reset");
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
