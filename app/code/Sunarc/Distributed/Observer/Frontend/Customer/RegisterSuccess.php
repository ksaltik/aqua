<?php


namespace Sunarc\Distributed\Observer\Frontend\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var CustomerRepositoryInterface
     */

    private $customerRepository;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
         CustomerRepositoryInterface $customerRepository
        )
    {
        $this->_request = $request;
        $this->customerRepository = $customerRepository;
    }
  
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ){      

        if(  $this->_request->getParam('customerid') != '' ) {
             $id = $this->_request->getParam('customerid');
        }elseif( $observer->getEvent()->getCustomer()->getId() != '' ) {
             $id = $observer->getEvent()->getCustomer()->getId();
        }else{
          return;
        }

        if($id){  
            $customer = $this->customerRepository->getById($id);
            $distributorRequest = $this->_request->getParam('distributor_request');
            if($distributorRequest){
                if($customer->getId()) {
                   /* $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $orderIds = $objectManager->create('Sunarc\AdvPermission\Helper\Data')->getRestrictOrderCollection($splitAttributeRestrictions);
                  */
                   //$customer->setDistributor($distributorRequest);
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $cust = $objectManager->create('Magento\Customer\Model\Customer')->load($id);
                   $cust->setDistributorRequest($distributorRequest);
                   $cust->save();

                   // $fixtureGroupId = $group->load($fixtureGroupCode, 'customer_group_code')->getId();
                   $customer->setCustomAttribute('distributor_request', $distributorRequest);
                }
            }
            $this->customerRepository->save($customer);
        }
    }
}