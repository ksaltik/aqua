<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Report\Marketing;

class Searchterm extends \Magento\Backend\Block\Widget\Grid
{
	/**
     * Get collection object
     *
     * @return \Magento\Framework\Data\Collection
     */
    public function getCollection()
    {
        $requestUri = $_SERVER['REQUEST_URI'];

        if (strpos($requestUri, 'admin/search/term/report') !== false) {

            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
            $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
            $user = $authSession->getUser();
            $userId = $user->getUserId();
            $userDetails = $userFactory->create()->load($userId);
            $role = $userDetails->getRole();

            if(  $role->getStoreIds() != '' && $role->getwebsiteId() != null ){
                $storeIds = explode(',', $role->getStoreIds());
                $searchCollection = $this->getData('dataSource')->addFieldToFilter('store_id',['in' => $storeIds]); 
                return $searchCollection;
            }else{
                return $this->getData('dataSource');
            }
        }else{
           return $this->getData('dataSource'); 
        }
        //return $this->getData('dataSource');
    }
}