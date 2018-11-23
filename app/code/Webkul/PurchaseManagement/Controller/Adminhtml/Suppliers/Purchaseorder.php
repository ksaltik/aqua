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

class Purchaseorder extends \Webkul\PurchaseManagement\Controller\Adminhtml\Suppliers
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $galleryModel = $this->_objectManager->create('Webkul\PurchaseManagement\Model\Suppliers');
        if ($this->getRequest()->getParam('id')) {
            $galleryModel->load($this->getRequest()->getParam('id'));
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $galleryModel->setData($data);
        }
        $this->_objectManager->get('Magento\Framework\Registry')->register('purchasemanagement_suppliers', $galleryModel);
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('purchasemanagement.suppliers.edit.tab.purchaseorder');
        return $resultLayout;
    }
}
