<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Report\Marketing;

class Abandonedcarts extends \Magento\Reports\Block\Adminhtml\Shopcart\Abandoned\Grid
{
	/**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Reports\Model\ResourceModel\Quote\Collection */
        $collection = $this->_quotesFactory->create();

        $filter = $this->getParam($this->getVarNameFilter(), []);

        if ($filter) {
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);
        }

        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $authSession = $_objectManager->get('Magento\Backend\Model\Auth\Session');
        $userFactory = $_objectManager->get('Magento\User\Model\UserFactory');
        $user = $authSession->getUser();
        $userId = $user->getUserId();
        $userDetails = $userFactory->create()->load($userId);
        $role = $userDetails->getRole();

        if(  $role->getStoreIds() != '' && $role->getwebsiteId() != null ){
            $storeIds = explode(',', $role->getStoreIds());
            $this->_storeIds = $storeIds;
        }

        if (!empty($data)) {
            $collection->prepareForAbandonedReport($this->_storeIds, $data);
        } else {
            $collection->prepareForAbandonedReport($this->_storeIds);
        }

        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->resolveCustomerNames();
        return $this;
    }
}