<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sunarc\Distributed\Controller\Adminhtml\Distributor;

use Magento\Framework\Controller\ResultFactory;

class MassAssignDistributer extends \Magento\Framework\App\Action\Action
{

    protected $_customer;
    protected $_customerFactory;
    protected $_pageFactory;
    protected $_customerGroupCollection;
    protected $groupFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $CustomerGroupCollection)
    {
        $this->_pageFactory = $pageFactory;
        $this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
        $this->_customerGroupCollection = $CustomerGroupCollection;
        $this->groupFactory = $groupFactory;
        return parent::__construct($context);
    }


    /**
     * Distributer list action
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

        $distributorGroupId = $this->_customerGroupCollection->addFieldToFilter('customer_group_code',['eq'=>'Distributor'])->getFirstItem()->getCustomerGroupId();

        if( !$distributorGroupId ){
            $group = $this->groupFactory->create();
            $group->setCode('Distributor')->setTaxClassId(3)->save();

            $fixtureGroupId = $group->load('Distributor', 'customer_group_code')->getId();
        }

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
            $customer->setCustomAttribute("distributor",'1');                 
            $customer->setGroupId($distributorGroupId);
            $customerRepository->save($customer);
            
            $k++;
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $k));

        $resultRedirect = $resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/distributor');

        return $resultRedirect;
    }
}
