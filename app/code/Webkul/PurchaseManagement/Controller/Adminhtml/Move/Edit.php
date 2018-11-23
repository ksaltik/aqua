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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Move;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Webkul\PurchaseManagement\Controller\Adminhtml\Move as MoveController;

class Edit extends MoveController
{
    protected $_suppliers;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\MoveFactory  $moveFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_move = $moveFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Incoming Products'));
        // $resultPage->getConfig()->getTitle()->prepend(
        //     $groupModel->getId() ? $groupModel->getGalleryCode() : __('New Supplier')
        // );
        $data=$this->_move->create();
        if ($this->getRequest()->getParam('id')) {
            $data->load($this->getRequest()->getParam('id'));
        }
        $this->_coreRegistry->register('purchasemanagement_data',$data);
        $resultPage->addBreadcrumb(__('Manage Group'), __('Manage Group'));
        $content = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Move\Edit');
        $resultPage->addContent($content);
        $left = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Move\Edit\Tabs');
        $resultPage->addLeft($left);
        return $resultPage;
    }
}
