<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
	/**
     * Initialize collection select
     * Redeclared for remove entity_type_id condition
     * in catalog_product_entity we store just products
     *
     * @return $this
     */
    protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {  
            $this->getSelect()->from(
                [self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()],
                null
            )->columns(
                ['status' => new \Zend_Db_Expr(ProductStatus::STATUS_ENABLED)]
            );
            $this->addAttributeToSelect($this->getResource()->getDefaultAttributes());
            if ($this->_catalogProductFlatState->getFlatIndexerHelper()->isAddChildData()) {
                $this->getSelect()->where('e.is_child=?', 0);
                $this->addAttributeToSelect(['child_id', 'is_child']);
            }
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');

        if( $authSession->getUser() != null ){

            $user = $authSession->getUser();
            $userId = $user->getUserId();
            $userDetails = $userFactory->create()->load($userId);
            $role = $userDetails->getRole();
            $storeIds = $role->getStoreIds();

            if( $role->getWebsiteId() != null ){
                $this->getSelect('cpw.website_id')->joinInner(
                    ['cpw' => $this->getTable('catalog_product_website')],
                    'e.entity_id = cpw.product_id'
                )->where(
                'cpw.website_id = '.$role->getWebsiteId()
                );
            }
        }

        return $this;
    }
}