<?php
namespace Sunarc\AdvPermission\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
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
        $splitAttributeRestrictions = explode(',', $user->getSplitattributeRestrictions());
        if (!empty($user->getSplitattributeRestrictions())) {
            $orderIds = $objectManager->create('Sunarc\AdvPermission\Helper\Data')->getRestrictOrderCollection($splitAttributeRestrictions);
            $this->addFieldToFilter('entity_id', ['in'=>$orderIds]);
        } else {
            $this;
        }
       // $logger->info();
        if ($role->getIsOrderRestrictByScope()) {
            $orderIds = $objectManager->create('Sunarc\AdvPermission\Helper\Data')->getRestrictOrderCollectionByScope();
            $this->addFieldToFilter('entity_id', ['in'=>$orderIds]);
        } else {
            $this;
        }
        parent::_renderFiltersBefore();
    }
}
