<?php


namespace Sunarc\Distributed\Observer\Adminhtml\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Framework\Controller\ResultFactory;


class Customersaveafter implements ObserverInterface
{
    /**
     * @var CustomerRepositoryInterface
     */

    private $customerRepository;
    protected $groupFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
         CustomerRepositoryInterface $customerRepository,
         GroupFactory $groupFactory
        )
    {
        $this->_request = $request;
        $this->customerRepository = $customerRepository;
        $this->groupFactory = $groupFactory;
    }
  
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ){      

        $id = $observer->getEvent()->getCustomer()->getId();
        $customer = $this->customerRepository->getById($id);
        $data = $this->_request->getPost();
        $distributorRequest = $data['customer']['distributor_request'];

        if($distributorRequest){
            if($customer->getId()) {

                $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cust = $_objectManager->create('Magento\Customer\Model\Customer')->load($id);

                $cust->setDistributorRequest($distributorRequest);
                $cust->save();
                $customer->setCustomAttribute('distributor_request', $distributorRequest);
            }
        }


        /*if($distributor){
            if($customer->getId()) {
               
               $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $cust = $_objectManager->create('Magento\Customer\Model\Customer')->load($id);


              $cust->setDistributor($distributor);
              $cust->save();
                $customer->setCustomAttribute('distributor', $distributor);
                $fixtureGroupCode = 'Distributor';
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $group = $objectManager->create(\Magento\Customer\Model\Group::class);
                $fixtureGroupId = $group->load($fixtureGroupCode, 'customer_group_code')->getId();
                //  $group_id = 5;
                if(!$fixtureGroupId)
                {
                    $group = $this->groupFactory->create();
                    $group
                        ->setCode('Distributor')
                        ->setTaxClassId(3)
                        ->save();
                }
                $fixtureGroupId = $group->load($fixtureGroupCode, 'customer_group_code')->getId();
                $customer->setGroupId($fixtureGroupId);
               $customer->setCustomAttribute('distributor', $distributor);
            }
        }*/
        $this->customerRepository->save($customer);
    }
}