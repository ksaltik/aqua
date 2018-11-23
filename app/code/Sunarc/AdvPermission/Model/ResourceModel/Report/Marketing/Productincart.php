<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Report\Marketing;

class Productincart extends \Magento\Reports\Block\Adminhtml\Shopcart\Product\Grid
{
	/**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $userDetails = $userFactory->create()->load($userId);
        $role = $userDetails->getRole();
        $storeIds = $role->getStoreIds();

        /** @var \Magento\Reports\Model\ResourceModel\Quote\Item\Collection $collection */
        //$collection = $this->quoteItemCollectionFactory->create();
        if( $role->getStoreIds() != '' || $role->getwebsiteId() != '' ){
            $collection = $this->quoteItemCollectionFactory->create()->addFieldToFilter('store_id',['in' => $storeIds]);

            $websiteProduct = [];
            foreach ($collection as $value) {
                $websiteProduc[] = $value->getProductId();
            }

            $collection = $this->quoteItemCollectionFactory->create();
            $collection->prepareActiveCartItems();
            $this->setCollection($collection);
            //return parent::_prepareCollection();
             return $this->getCollection()->addFieldToFilter('product_id',['in' => $websiteProduc])->getData();
        }else{
            $collection = $this->quoteItemCollectionFactory->create();
            $collection->prepareActiveCartItems();
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }     
    }

}