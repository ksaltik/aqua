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
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends Action
{
    protected $_suppliers;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Webkul\PurchaseManagement\Model\SuppliersFactory  $suppliersFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_suppliers = $suppliersFactory;
        $this->_date = $date;
    }

    public function execute()
    {
        try {
            $time = (string)$this->_date->gmtDate();
            $resultRedirect = $this->resultRedirectFactory->create();
            $data = $this->getRequest()->getParams();
            unset($data['created_at']);
            if ($data) {
                $id = (int) $this->getRequest()->getParam('id');
                $model = $this->_suppliers->create();
                $data['updated_at'] = $time;
                $data['name'] = htmlspecialchars($data['name']);
                if(preg_match('/[^a-z0-9]+/i', $data['zip'])) {
                    $this->messageManager->addError(__('Use of Special symbols is not allowed in zip code'));
                    $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                    return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                }
                if (($id!=0)) {
                    $model->addData($data)->setId($id)->save();
                    $this->messageManager->addSuccess(__('Supplier saved succesfully.'));
                } else {
                    $data['created_at'] = $time;
                    $data['active']=0;
                    unset($data['id']);
                    $collection = $this->_suppliers->create()->getCollection()->addFieldToFilter('email',$data['email'])->getSize();
                    if (!$collection) {
                        $model->setData($data)->save();
                        $this->messageManager->addSuccess(__('Supplier saved succesfully.'));
                    } else {
                        $this->messageManager->addError(__('Email ID must be unique.'));
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }


        return $resultRedirect->setPath('*/*/');
    }
}
