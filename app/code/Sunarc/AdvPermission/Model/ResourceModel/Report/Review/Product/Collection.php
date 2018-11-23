<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Report\Review\Product;

class Collection extends \Magento\Reports\Model\ResourceModel\Review\Product\Collection
{
	/**
     * Join review table to result
     *
     * @return $this
     */
    protected function _joinReview()
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $userDetails = $userFactory->create()->load($userId);
        $role = $userDetails->getRole();

        $storeIds = $role->getStoreIds() ;

        if( $role->getwebsiteId() && $role->getwebsiteId() != '' ){
            $subSelect = clone $this->getSelect('r.website_id')->join(
                ['w' => $this->getTable('catalog_product_website')],
                'e.entity_id = w.product_id'
            )->where(
                'website_id = '.$role->getwebsiteId()
            );
        }else{
            $subSelect = clone $this->getSelect();
        }

        $subSelect->reset()->from(
            ['rev' => $this->getTable('review')],
            'COUNT(DISTINCT rev.review_id)'
        )->where(
            'e.entity_id = rev.entity_pk_value'
        );

        $this->addAttributeToSelect('name');

        $this->getSelect()->join(
            ['r' => $this->getTable('review')],
            'e.entity_id = r.entity_pk_value',
            [
                'review_cnt' => new \Zend_Db_Expr(sprintf('(%s)', $subSelect)),
                'created_at' => 'MAX(r.created_at)'
            ]
        )->group(
            'e.entity_id'
        );

        $joinCondition = [
            'e.entity_id = table_rating.entity_pk_value',
            $this->getConnection()->quoteInto('table_rating.store_id > ?', 0),
        ];

        $sumPercentField = new \Zend_Db_Expr("SUM(table_rating.percent)");
        $sumPercentApproved = new \Zend_Db_Expr('SUM(table_rating.percent_approved)');
        $countRatingId = new \Zend_Db_Expr('COUNT(table_rating.rating_id)');

        $this->getSelect()->joinLeft(
            ['table_rating' => $this->getTable('rating_option_vote_aggregated')],
            implode(' AND ', $joinCondition),
            [
                'avg_rating' => new \Zend_Db_Expr(sprintf('%s/%s', $sumPercentField, $countRatingId)),
                'avg_rating_approved' => new \Zend_Db_Expr(sprintf('%s/%s', $sumPercentApproved, $countRatingId))
            ]
        );

        return $this;
    }
}