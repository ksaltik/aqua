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

use Magento\Backend\App\Action;

class Save extends Action
{
    /**
     * @var \Webkul\MpRmaSystem\Model\ReasonsFactory
     */
    protected $_reasons;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Webkul\MpRmaSystem\Model\ReasonsFactory $reasons
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\MpRmaSystem\Model\ReasonsFactory $reasons
    ) {
        $this->_reasons = $reasons;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_MpRmaSystem::reasons');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
            $id = (int) $this->getRequest()->getParam('id');
            $resultRedirect = $this->resultRedirectFactory->create();
            $model = $this->_reasons->create();
            if ($id) {
                $model->addData($data)->setId($id)->save();
                $this->messageManager->addSuccess(__('Reason edited successfully.'));
            } else {
                $model->setData($data)->save();
                $this->messageManager->addSuccess(__('Reason saved successfully.'));
            }
        } else {
            $error = 'There was some error while processing your request.';
            $this->messageManager->addError(__($error));
            return $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect->setPath('*/*/');
    }
}
