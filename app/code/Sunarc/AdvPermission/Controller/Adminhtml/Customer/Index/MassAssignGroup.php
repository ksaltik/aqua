<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sunarc\AdvPermission\Controller\Adminhtml\Customer\Index;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassAssignGroup
 */
class MassAssignGroup extends \Magento\Customer\Controller\Adminhtml\Index\MassAssignGroup
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerRepository = $customerRepository;
    }

    /**
     * Customer mass assign group action
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $customersUpdated = 0;

        foreach ($collection->getAllIds() as $customerId) {

            // Verify customer exists
            $customer = $this->customerRepository->getById($customerId);
            $prevCustomerGroup = $customer->getGroupId();

            $customer->setGroupId($this->getRequest()->getParam('group'));

            //$this->customerRepository->save($customer);
            if( $this->customerRepository->save($customer) ){

                if( $this->getRequest()->getParam('group') != $prevCustomerGroup ){

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
                
                    $groupRepository  = $objectManager->create('\Magento\Customer\Api\GroupRepositoryInterface');
                    $newGroupName = $groupRepository->getById($this->getRequest()->getParam('group'))->getCode();
                    $oldGroupName = $groupRepository->getById($prevCustomerGroup)->getCode();

                    $transportBuilder = $objectManager->get('Magento\Framework\Mail\Template\TransportBuilder');
                    $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

                    $storeEmail= $scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                    $storeName= $scopeConfig->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                    $templateOptions = array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getStore()->getId());
                    $templateVars = array(
                                        'store' => $store->getStore(),
                                        'email' => $customer->getEmail(),
                                        'name' => $customer->getFirstname().' '.$customer->getLastname(),
                                        'newGroup' => $newGroupName,
                                        'oldGroup' => $oldGroupName,
                                    );
                    $from = array('email' => $storeEmail, 'name' => $storeName);
                    $to = array($customer->getEmail());
                    $transport = $transportBuilder->setTemplateIdentifier('customer_change_group_email_template')
                                    ->setTemplateOptions($templateOptions)
                                    ->setTemplateVars($templateVars)
                                    ->setFrom($from)
                                    ->addTo($to)
                                    ->getTransport();
                    $transport->sendMessage();
                }
            }

            $customersUpdated++;
        }

        if ($customersUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $customersUpdated));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
