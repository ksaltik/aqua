<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Review\Product;

class Collection extends \Magento\Review\Model\ResourceModel\Review\Product\Collection
{	
	/**
     * Join fields to entity
     *
     * @return $this
     */
    protected function _joinFields()
    {

        $reviewTable = $this->_resource->getTableName('review');
        $reviewDetailTable = $this->_resource->getTableName('review_detail');

        $this->addAttributeToSelect('name')->addAttributeToSelect('sku');

        $this->getSelect()->join(
            ['rt' => $reviewTable],
            'rt.entity_pk_value = e.entity_id',
            ['rt.review_id', 'review_created_at' => 'rt.created_at', 'rt.entity_pk_value', 'rt.status_id']
        )->join(
            ['rdt' => $reviewDetailTable],
            'rdt.review_id = rt.review_id',
            ['rdt.title', 'rdt.nickname', 'rdt.detail', 'rdt.customer_id', 'rdt.store_id']
        );

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');

        if( $authSession->getUser() != null ){

            $user = $authSession->getUser();
            $userId = $user->getUserId();
            $userDetails = $userFactory->create()->load($userId);
            $role = $userDetails->getRole();

            if( $role->getWebsiteId() != null && $role->getStoreIds() != '' ){
                $storeIds = explode(',', $role->getStoreIds());

                $this->getSelect()->where(
                    'rdt.store_id IN (?)',
                    $storeIds
                );
                return $this;
            }
        }

        return $this;
    }

}