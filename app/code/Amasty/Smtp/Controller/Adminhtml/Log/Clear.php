<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Smtp
 */


namespace Amasty\Smtp\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;

class Clear extends Action
{
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Smtp::reports_sent');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_objectManager->get('Amasty\Smtp\Model\ResourceModel\Log')->truncate();

            $this->messageManager->addSuccess(__('Emails log has been cleared.'));
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }
    }
}