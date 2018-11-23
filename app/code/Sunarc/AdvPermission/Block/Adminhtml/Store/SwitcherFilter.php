<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sunarc\AdvPermission\Block\Adminhtml\Store;
//use Magento\Backend\Block\Store\Switcher;

/**
 * Store switcher block
 *
 * @api
 * @since 100.0.2
 */
class SwitcherFilter extends \Magento\Backend\Block\Store\Switcher
{

    /**
     * @return \Magento\Store\Model\ResourceModel\Website\Collection
     */

/*
    public function getWebsites()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $store_ids = $objectManager->create('\Sunarc\AdvPermission\Helper\Data');
        $model = $objectManager->create('\Magento\Store\Model\StoreRepository');
        $stores = $model->getList();

        $websites = $this->_storeManager->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) {
            $websites = array_intersect_key($websites, array_flip($websiteIds));
        }
        return $websites;
    }*/
    public function getStores($group)
    {
        if (!$group instanceof \Magento\Store\Model\Group) {
            $group = $this->_storeManager->getGroup($group);
        }
        $stores = $group->getStores();
        //if ($storeIds = $this->getStoreIds()) {
            foreach (array_keys($stores) as $storeId) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('Your text message'.$storeId);
                // $logger->info(print_r($stores, true));
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $storeIdsnew = $objectManager->create('Sunarc\AdvPermission\Helper\Data')->getScopeValuesForForm();
                $storeID = $storeId;
                $isStoreAllowed = true;
                if(!empty($storeIdsnew))
                {
                    $isStoreAllowed =   (bool)in_array($storeID ,array(2));
                }
                if(!$isStoreAllowed){
                    $logger->info('Your text messageallow'.$storeId);
                    unset($stores[$storeId]);
                }
            }
        //}
        return $stores;
    }
    public function isStoreAllowed($storeID)
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Your text messagestore');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('\Sunarc\AdvPermission\Helper\Data');
        $storeIds = $helper->getScopeValuesForForm();
       // return (bool) in_array($storeID , $storeIds);
        return $storeIds;
    }
}
