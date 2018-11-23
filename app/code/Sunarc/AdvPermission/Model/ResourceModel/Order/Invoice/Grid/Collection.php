<?php
namespace Sunarc\AdvPermission\Model\ResourceModel\Order\Invoice\Grid;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Grid\Collection as OriginalCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Sales\Ui\Component\DataProvider\Document;
use Psr\Log\LoggerInterface as Logger;

class Collection extends OriginalCollection
{
    protected $_authSession;
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
    
        $this->_authSession = $authSession;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
    }

    protected function _renderFiltersBefore()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $user = $this->_authSession->getUser();
        $role = $this->_authSession->getUser()->getRole();
        $invoiceGridCollection=$this;
        if ($user->getSplitattributeRestrictions()) {
            $this->filterBySplitattributeRestriction($user, $invoiceGridCollection);
        } else {
            $this;
        }
        if ($role->getIsInvoiceRestrictByScope()) {
            $this->filterByScopeRestriction($role, $invoiceGridCollection);
        } else {
            $this;
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Apply the split attribute filters for admin user
     *
     * @param $user
     * @param $collection
     * @return mixed
     */
    protected function filterBySplitattributeRestriction($user, $collection)
    {
        $splitAttributeRestrictions = explode(',', $user->getSplitattributeRestrictions());
        $collection->getSelect()
            ->reset(\Zend_Db_Select::WHERE);
        $collection->getSelect()
            ->distinct()
            ->join(['soi' => 'sales_order_item'], 'main_table.order_id = soi.order_id', ['split_attribute_value','order_id']);
        $orConditions = [];
        foreach ($splitAttributeRestrictions as $splitAttributeRestriction) {
            $orConditions[] = ['eq' => (int)$splitAttributeRestriction];
        }

        $collection->addFieldToFilter(['soi.split_attribute_value'], [$orConditions]);
        $collection->addFilterToMap('order_id', 'main_table.order_id');

        return $collection;
    }

    protected function filterByScopeRestriction($role, $collection)
    {
        $storeIds = explode(',', $role->getStoreIds());
        $collection->getSelect()
            ->reset(\Zend_Db_Select::WHERE);
        $collection->getSelect()
            ->distinct()
            ->join(['soi' => 'sales_order_item'], 'main_table.order_id = soi.order_id', ['store_id','order_id']);
        $orConditions = [];
        foreach ($storeIds as $storeId) {
            $orConditions[] = ['eq' => (int)$storeId];
        }

        $collection->addFieldToFilter(['soi.store_id'], [$orConditions]);
        $collection->addFilterToMap('order_id', 'main_table.order_id');

        return $collection;
    }


}
