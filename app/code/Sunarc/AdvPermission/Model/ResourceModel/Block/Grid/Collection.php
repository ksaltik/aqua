<?php
namespace Sunarc\AdvPermission\Model\ResourceModel\Block\Grid;

use Magento\Cms\Model\ResourceModel\Block\Grid\Collection as OriginalCollection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Cms\Model\ResourceModel\Block\Collection as BlockCollection;

class Collection extends OriginalCollection
{

    protected $_authSession;
//protected $mainTable = 'cms_block';
    public function __construct(\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\EntityManager\MetadataPool $metadataPool,
       $mainTable, $eventPrefix, $eventObject, $resourceModel, $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class, $connection = null, //\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        \Magento\Backend\Model\Auth\Session $authSession)
    {

        $this->_authSession = $authSession;
        parent::__construct(
            $entityFactory,
        $logger,
        $fetchStrategy,
        $eventManager,
        $storeManager,
        $metadataPool,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        $connection = null,
        null
        );
       $this->_eventPrefix = $eventPrefix;
       $this->_eventObject = $eventObject;
      $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }
    protected function _renderFiltersBefore()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $user = $this->_authSession->getUser();
//        $role = $this->_authSession->getUser()->getRole();
//        $splitAttributeRestrictions = explode(',', $user->getSplitattributeRestrictions());

        // $logger->info();

        $orderIds = $objectManager->create('Sunarc\AdvPermission\Helper\Data')->getRestrictBlockCollectionByScope();
        if(isset($orderIds) && $orderIds)
        $this->addFieldToFilter('block_id', ['in'=>$orderIds]);

      //  $logger->info(print_r($this->getData(), true));



        parent::_renderFiltersBefore();
    }
}