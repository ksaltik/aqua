<?php

namespace Sunarc\AdvPermission\Model\System;

class Store extends \Magento\Store\Model\System\Store
{
    /**
     * @var bool
     */
    private $_isAdminScopeAllowed = true;
    
	/**
     * Website label/value array getter, compatible with form dropdown options
     *
     * @param bool $empty
     * @param bool $all
     * @return array
     */
    public function getWebsiteValuesForForm($empty = false, $all = false)
    {
        $options = [];
        if ($empty) {
            $options[] = ['label' => __('-- Please Select --'), 'value' => ''];
        }
        if ($all && $this->_isAdminScopeAllowed) {
            $options[] = ['label' => __('Admin'), 'value' => 0];
        }

        foreach ($this->_websiteCollection as $website) {
            $options[] = ['label' => $website->getName(), 'value' => $website->getId()];
        }


        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');

        if( $authSession->getUser() ){
            $user = $authSession->getUser();
            $userId = $user->getUserId();
            $userDetails = $userFactory->create()->load($userId);
            $role = $userDetails->getRole();

            if( $role->getwebsiteId() && $role->getwebsiteId() != '' ){
                foreach ($options as $websiteArr) {
                    if( $websiteArr['value'] === $role->getwebsiteId() ){
                        unset($options);
                        $options[] = $websiteArr;
                        return $options;
                    }
                }
            } 
        }
        return $options;
    }

    /**
     * Retrieve store values for form
     *
     * @param bool $empty
     * @param bool $all
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getStoreValuesForForm($empty = false, $all = false)
    {
        $options = [];
        if ($empty) {
            $options[] = ['label' => '', 'value' => ''];
        }
        if ($all && $this->_isAdminScopeAllowed) {
            $options[] = ['label' => __('All Store Views'), 'value' => 0];
        }

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

        foreach ($this->_websiteCollection as $website) {
            $websiteShow = false;

            foreach ($this->_groupCollection as $group) {
                if ($website->getId() != $group->getWebsiteId()) {
                    continue;
                }
                $groupShow = false;
                foreach ($this->_storeCollection as $store) {
                    if ($group->getId() != $store->getGroupId()) {
                        continue;
                    }
                    if (!$websiteShow) {
                        $options[] = ['label' => $website->getName(), 'value' => []];
                        $websiteShow = true;
                    }
                    if (!$groupShow) {
                        $groupShow = true;
                        $values = [];
                    }
                    $values[] = [
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $store->getName(),
                        'value' => $store->getId(),
                    ];
                }
                if ($groupShow) {
                    $options[] = [
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $group->getName(),
                        'value' => $values,
                    ];
                }
            }

            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
            $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');

            if( $authSession->getUser() && $authSession->getUser() != null ){
                $user = $authSession->getUser();
                $userId = $user->getUserId();
                $userDetails = $userFactory->create()->load($userId);
                $role = $userDetails->getRole();

                if( $role->getwebsiteId() != '' && $role->getwebsiteId() == $website->getId() ){

                    unset($options);
                    foreach ($this->_groupCollection as $group) {
                        if ($website->getId() != $group->getWebsiteId()) {
                            continue;
                        }
                        $groupShow = false;
                        foreach ($this->_storeCollection as $store) {
                            if ($group->getId() != $store->getGroupId()) {
                                continue;
                            }
                            if (!$websiteShow) {
                                $options[] = ['label' => $website->getName(), 'value' => []];
                                $websiteShow = true;
                            }
                            if (!$groupShow) {
                                $groupShow = true;
                                $values = [];
                            }
                            $values[] = [
                                'label' => str_repeat($nonEscapableNbspChar, 4) . $store->getName(),
                                'value' => $store->getId(),
                            ];
                        }
                        if ($groupShow) {
                            $options[] = [
                                'label' => str_repeat($nonEscapableNbspChar, 4) . $group->getName(),
                                'value' => $values,
                            ];
                        }
                    } 
                    return $options;
                }
            }
        }

        return $options;
    }
}