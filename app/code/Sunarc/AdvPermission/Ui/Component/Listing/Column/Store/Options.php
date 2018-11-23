<?php

namespace Sunarc\AdvPermission\Ui\Component\Listing\Column\Store;

class Options extends \Magento\Store\Ui\Component\Listing\Column\Store\Options
{
	/**
     * Generate current options
     *
     * @return void
     */
    protected function generateCurrentOptions()
    {
        $websiteCollection = $this->systemStore->getWebsiteCollection();
        $groupCollection = $this->systemStore->getGroupCollection();
        $storeCollection = $this->systemStore->getStoreCollection();
        /** @var \Magento\Store\Model\Website $website */
        foreach ($websiteCollection as $website) {

            $groups = [];

            $requestUri = $_SERVER['REQUEST_URI'];
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
            $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');

            if( $authSession->getUser() != null ){
                $user = $authSession->getUser();
                $userId = $user->getUserId();
                $userDetails = $userFactory->create()->load($userId);
                $role = $userDetails->getRole();

                if ( (strpos($requestUri, '/admin/') !== false) && ($role->getWebsiteId() != null)   ) {
                    
                    if( $role->getWebsiteId() == $website->getId() ){

                        foreach ($groupCollection as $group) {
                            if ($group->getWebsiteId() == $website->getId()) {
                                $stores = [];
                                /** @var  \Magento\Store\Model\Store $store */
                                foreach ($storeCollection as $store) {
                                    if ($store->getGroupId() == $group->getId()) {
                                        $name = $this->escaper->escapeHtml($store->getName());
                                        $stores[$name]['label'] = str_repeat(' ', 8) . $name;
                                        $stores[$name]['value'] = $store->getId();
                                    }
                                }
                                if (!empty($stores)) {
                                    $name = $this->escaper->escapeHtml($group->getName());
                                    $groups[$name]['label'] = str_repeat(' ', 4) . $name;
                                    $groups[$name]['value'] = array_values($stores);
                                }
                            }
                        }
                    }
                }else{ 
                    /** @var \Magento\Store\Model\Group $group */
                    foreach ($groupCollection as $group) {
                        if ($group->getWebsiteId() == $website->getId()) {
                            $stores = [];
                            /** @var  \Magento\Store\Model\Store $store */
                            foreach ($storeCollection as $store) {
                                if ($store->getGroupId() == $group->getId()) {
                                    $name = $this->escaper->escapeHtml($store->getName());
                                    $stores[$name]['label'] = str_repeat(' ', 8) . $name;
                                    $stores[$name]['value'] = $store->getId();
                                }
                            }
                            if (!empty($stores)) {
                                $name = $this->escaper->escapeHtml($group->getName());
                                $groups[$name]['label'] = str_repeat(' ', 4) . $name;
                                $groups[$name]['value'] = array_values($stores);
                            }
                        }
                    }
                }
            }else{ 
                /** @var \Magento\Store\Model\Group $group */
                foreach ($groupCollection as $group) {
                    if ($group->getWebsiteId() == $website->getId()) {
                        $stores = [];
                        /** @var  \Magento\Store\Model\Store $store */
                        foreach ($storeCollection as $store) {
                            if ($store->getGroupId() == $group->getId()) {
                                $name = $this->escaper->escapeHtml($store->getName());
                                $stores[$name]['label'] = str_repeat(' ', 8) . $name;
                                $stores[$name]['value'] = $store->getId();
                            }
                        }
                        if (!empty($stores)) {
                            $name = $this->escaper->escapeHtml($group->getName());
                            $groups[$name]['label'] = str_repeat(' ', 4) . $name;
                            $groups[$name]['value'] = array_values($stores);
                        }
                    }
                }
            }

            if (!empty($groups)) {
                $name = $this->escaper->escapeHtml($website->getName());
                $this->currentOptions[$name]['label'] = $name;
                $this->currentOptions[$name]['value'] = array_values($groups);
            }
        }
    }
}