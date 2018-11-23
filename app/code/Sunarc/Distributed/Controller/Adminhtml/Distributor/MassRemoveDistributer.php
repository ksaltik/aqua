<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sunarc\Distributed\Controller\Adminhtml\Distributor;

use Magento\Framework\Controller\ResultFactory;

class MassRemoveDistributer extends \Magento\Framework\App\Action\Action
{

	protected $_customer;
    protected $_customerFactory;
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers)
    {
        $this->_pageFactory = $pageFactory;
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
        return parent::__construct($context);
    }


    /**
     * Customers list action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {

    	$customerIDS = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
 
        $customerRepository = $objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
 
        $context = $objectManager->get('\Magento\Framework\App\Action\Context');

        $resultFactory = $context->getResultFactory();

        if( isset($customerIDS) && count($customerIDS) > 0 ){

            $selectedIDS = array_values($customerIDS);
            
            $custCollection = $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("distributor_request", array("eq" => "1"))
                ->addAttributeToFilter("entity_id", array("in" => array($selectedIDS)));             

        }elseif( isset($excluded) && $excluded == 'false' ){

            $custCollection = $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("distributor_request", array("eq" => "1"));

        }elseif( isset($excluded) && is_array($excluded) ){
            
            $excludedIDS = array_values($excluded);
            
            $custCollection = $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("distributor_request", array("eq" => "1"))
                ->addAttributeToFilter("entity_id", array("nin" => array($excludedIDS)));             
        }
        else{
            $this->messageManager->addError(__('Something went wrong! Please try again.'));
        }

        $k =0;

        foreach ($custCollection as $customerObj) {

            $customer = $customerRepository->getById($customerObj->getId());             
            $customer->setCustomAttribute("distributor",'0');                 
            $customer->setGroupId(1);
            $customerRepository->save($customer);
            
            $k++;
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $k));

        $resultRedirect = $resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/distributor');

        return $resultRedirect;

    }
}
