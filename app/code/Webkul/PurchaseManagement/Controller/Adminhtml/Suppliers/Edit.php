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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Suppliers;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Action;

class Edit extends \Webkul\PurchaseManagement\Controller\Adminhtml\Suppliers
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
        /** @var \Webkul\PurchaseManagement\Model\Faqgroup $groupModel */
        $groupModel = $this->_suppliers->create();
        if ($this->getRequest()->getParam('id')) {
            $groupModel->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $groupModel->setData($data);
        }
        $this->_objectManager->get('Magento\Framework\Registry')->register('purchasemanagement_suppliers', $groupModel);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Supplier'));
        $resultPage->getConfig()->getTitle()->prepend(
            $groupModel->getId() ? $groupModel->getName() : __('New Supplier')
        );
        $resultPage->addBreadcrumb(__('Manage Group'), __('Manage Group'));
        $content = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Suppliers\Edit');
        $resultPage->addContent($content);
        $left = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Suppliers\Edit\Tabs');
        $resultPage->addLeft($left);
        return $resultPage;
    }
}
