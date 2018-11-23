<?php
/**
 * PurchaseManagement Admin  Shipments Index Controller
 *
 * @category    Webkul
 * @package     Webkul_PurchaseManagement
 * @author      Webkul Software Private Limited
 *
 */
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Picking;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_PurchaseManagement::purchase');
        $resultPage->getConfig()->getTitle()->prepend(__('Incoming Shipments'));

        return $resultPage;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_PurchaseManagement::shipments');
    }
}
