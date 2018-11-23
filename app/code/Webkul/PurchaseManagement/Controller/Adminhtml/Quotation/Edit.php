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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Quotation;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Webkul\PurchaseManagement\Controller\Adminhtml\Quotation as QuotationController;


class Edit extends QuotationController
{
    protected $_suppliers;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory  $suppliersFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_suppliers = $suppliersFactory;
    }

    public function execute()
    {   
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Quotation'));
        // $resultPage->getConfig()->getTitle()->prepend(
        //     $groupModel->getId() ? $groupModel->getGalleryCode() : __('New Supplier')
        // );
        $resultPage->addBreadcrumb(__('Manage Group'), __('Manage Group'));
        $content = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Quotation\Create');
        $resultPage->addContent($content);
        $left = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Quotation\Edit\Createtabs');
        $resultPage->addLeft($left);
        return $resultPage;
    }
}
