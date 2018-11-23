<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Category;

class Tree extends \Magento\Catalog\Model\ResourceModel\Category\Tree
{
	/**
     * Add data to collection
     *
     * @param Collection $collection
     * @param boolean $sorted
     * @param array $exclude
     * @param boolean $toLoad
     * @param boolean $onlyActive
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addCollectionData(
        $collection = null,
        $sorted = false,
        $exclude = [],
        $toLoad = true,
        $onlyActive = false
    ) {
        if ($collection === null) {
            $collection = $this->getCollection($sorted);
        } else {
            $this->setCollection($collection);
        }

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
        
        if( $authSession->getUser() ){
            $user = $authSession->getUser();
            $userId = $user->getUserId();
            $userDetails = $userFactory->create()->load($userId);
            $role = $userDetails->getRole();

            if( $role->getStoreIds() && $role->getWebsiteId() ){
                $Store = $_objectManager->get('Magento\Store\Model\Store');
                $StoreManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($role->getStoreIds());

                $rootCategoryId =   $StoreManager->getRootCategoryId($role->getStoreIds());
                $categoryRepository = $_objectManager->get('Magento\Catalog\Model\CategoryRepository');

                $categories = $categoryRepository->get($rootCategoryId)->getChildrenCategories();
                $categoryIds[] = $rootCategoryId;

                if( $categories  ){
                    foreach ($categories as $key => $value) {
                        $categoryIds[] = $value->getEntityId(); 
                        if( $value->hasChildren()) {
                            $childSubcategories = $categoryRepository->get($value->getId())->getChildrenCategories();
                            foreach($childSubcategories as $childSubcategorie) {
                                $categoryIds[] = $childSubcategorie->getId();
                                if( $childSubcategorie->hasChildren()) {
                                    $subchildSubcategories = $categoryRepository->get($childSubcategorie->getId())->getChildrenCategories();
                                    foreach($subchildSubcategories as $subchildSubcategorie) {
                                        $categoryIds[] = $subchildSubcategorie->getId();
                                    }
                                }  
                            }
                        }  
                    }                      
                }              
                $collection->addFieldToFilter('entity_id', ['IN' => [$categoryIds]] );
            }
        }

        if (!is_array($exclude)) {
            $exclude = [$exclude];
        }

        $nodeIds = [];
        foreach ($this->getNodes() as $node) {
            if (!in_array($node->getId(), $exclude)) {
                $nodeIds[] = $node->getId();
            }
        }
        $collection->addIdFilter($nodeIds);
        if ($onlyActive) {
            $disabledIds = $this->_getDisabledIds($collection, $nodeIds);
            if ($disabledIds) {
                $collection->addFieldToFilter('entity_id', ['nin' => $disabledIds]);
            }
            $collection->addAttributeToFilter('is_active', 1);
            $collection->addAttributeToFilter('include_in_menu', 1);
        }

        if ($this->_joinUrlRewriteIntoCollection) {
            $collection->joinUrlRewrite();
            $this->_joinUrlRewriteIntoCollection = false;
        }

        if ($toLoad) {
            $collection->load();

            foreach ($collection as $category) {
                if ($this->getNodeById($category->getId())) {
                    $this->getNodeById($category->getId())->addData($category->getData());
                }
            }

            foreach ($this->getNodes() as $node) {
                if (!$collection->getItemById($node->getId()) && $node->getParent()) {
                    $this->removeNode($node);
                }
            }
        }

        return $this;
    }
}