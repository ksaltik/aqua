<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpRmaSystem
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpRmaSystem\Controller\Adminhtml\Rma;

class Product extends \Webkul\MpRmaSystem\Controller\Adminhtml\Rma
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @var \Webkul\MpRmaSystem\Model\DetailsFactory
     */
    protected $_details;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Webkul\MpRmaSystem\Model\DetailsFactory $details
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Webkul\MpRmaSystem\Model\DetailsFactory $details,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_backendSession = $context->getSession();
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_details = $details;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $rmaModel = $this->_details->create();
        if ($this->getRequest()->getParam('id')) {
            $rmaModel->load($this->getRequest()->getParam('id'));
        }

        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $rmaModel->setData($data);
        }

        $this->_coreRegistry->register('mprmasystem_rma', $rmaModel);
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('mprmasystem.rma.view.tab.product');
        return $resultLayout;
    }
}
