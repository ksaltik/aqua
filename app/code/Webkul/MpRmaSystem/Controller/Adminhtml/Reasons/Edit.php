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
namespace Webkul\MpRmaSystem\Controller\Adminhtml\Reasons;

use Webkul\MpRmaSystem\Controller\Adminhtml\Reasons as ReasonsController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends ReasonsController
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var \Webkul\MpRmaSystem\Model\ReasonsFactory
     */
    protected $_reasons;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Webkul\MpRmaSystem\Model\ReasonsFactory $reasons
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\MpRmaSystem\Model\ReasonsFactory $reasons,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_backendSession = $context->getSession();
        $this->_reasons = $reasons;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $reasonModel = $this->_reasons->create();
        if ($this->getRequest()->getParam('id')) {
            $reasonModel->load($this->getRequest()->getParam('id'));
        }

        $data = $this->_backendSession->getFormData(true);
        if (!empty($data)) {
            $reasonModel->setData($data);
        }

        $this->_coreRegistry->register('mprmasystem_reasons', $reasonModel);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Reasons'));
        $resultPage->getConfig()->getTitle()->prepend(
            $reasonModel->getId() ? $reasonModel->getTitle() : __('New Reason')
        );
        $resultPage->addBreadcrumb(__('Manage Reasons'), __('Manage Reasons'));
        $block = 'Webkul\MpRmaSystem\Block\Adminhtml\Reasons\Edit';
        $content = $resultPage->getLayout()->createBlock($block);
        $resultPage->addContent($content);
        return $resultPage;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_MpRmaSystem::reasons');
    }
}
