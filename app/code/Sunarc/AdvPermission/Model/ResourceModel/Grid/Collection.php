<?php
namespace Sunarc\AdvPermission\Model\ResourceModel\Grid;

use Magento\Customer\Model\ResourceModel\Grid\Collection as OriginalCollection;
use Magento\Customer\Ui\Component\DataProvider\Document;
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
            $customerIds = $objectManager->create('Sunarc\AdvPermission\Helper\Data')->getRestrictCustomerCollectionByScope();
            $this->addFieldToFilter('entity_id', ['in'=>$customerIds]);
      /*  } else {
            $this;
        }*/
        parent::_renderFiltersBefore();
    }
}
