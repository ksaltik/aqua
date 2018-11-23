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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Webkul\PurchaseManagement\Controller\Adminhtml\Order as OrderController;


class View extends OrderController
{
    protected $_suppliers;

    protected $_order;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory  $suppliersFactory,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_suppliers = $suppliersFactory;
        $this->_order= $orderFactory;
    }

    public function execute()
    {
        $groupModel = $this->_order->create();
        if ($this->getRequest()->getParam('id')) {
            $groupModel->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $groupModel->setData($data);
        }
        $this->_objectManager->get('Magento\Framework\Registry')->register('purchasemanagement_data', $groupModel);

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Order'));
        $resultPage->addBreadcrumb(__('Manage Group'), __('Manage Group'));
        $content = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Order\Edit');
        $resultPage->addContent($content);
        $left = $resultPage->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Order\Edit\Tabs');
        $resultPage->addLeft($left);
        return $resultPage;
    }
}
