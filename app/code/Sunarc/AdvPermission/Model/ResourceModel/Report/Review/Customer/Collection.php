<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Report\Review\Customer;

class Collection extends \Magento\Reports\Model\ResourceModel\Review\Customer\Collection
{
	protected function _joinCustomers()
    {
    	$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $userDetails = $userFactory->create()->load($userId);
        $role = $userDetails->getRole();

        /** @var $connection \Magento\Framework\DB\Adapter\AdapterInterface */
        $connection = $this->getConnection();
        //Prepare fullname field result
        $customerFullname = $connection->getConcatSql(['customer.firstname', 'customer.lastname'], ' ');

        $this->getSelect()->reset(
            \Magento\Framework\DB\Select::COLUMNS
        )->joinInner(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = detail.customer_id',
            []
        )->columns(
            [
                'customer_id' => 'detail.customer_id',
                'customer_name' => $customerFullname,
                'review_cnt' => 'COUNT(main_table.review_id)',
            ]
        )->group(
            'detail.customer_id'
        );

        if( $role->getwebsiteId() && $role->getwebsiteId() != '' ){
            $this->getSelect()->where(
                'website_id IN (?)',
                array($role->getwebsiteId())
            );  
        }

        return $this;
    }
}