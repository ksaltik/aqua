<?php

namespace Sunarc\AdvPermission\Model\Magento\CatalogRule\ResourceModel\Grid;


class Collection extends \Magento\CatalogRule\Model\ResourceModel\Rule\Collection
{
	/**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();

        //return $this;

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $userDetails = $userFactory->create()->load($userId);
        $role = $userDetails->getRole();
        

        if( $role->getwebsiteId() && $role->getwebsiteId() != '' ){
            $websiteId[] = $role->getwebsiteId();
            $select = $this->getConnection()->select()->from(
                $this->getTable('catalogrule_website')
            )->where(
                'website_id IN (?)',
                array($websiteId)
            );
            $associatedEntities = $this->getConnection()->fetchAll($select);

            $ruleIds = [];
            foreach ($associatedEntities as $value) {
                $ruleIds[] = $value['rule_id'];
            }

            return $this->addFieldToFilter('rule_id', $ruleIds);
        }else{
            return $this;
        }        
    }
}
	
	