<?php

namespace Sunarc\Distributed\Model\ResourceModel\Grid;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;

use Magento\Customer\Model\ResourceModel\Grid\Collection as OriginalCollection;


class Collection extends OriginalCollection
{


protected function _renderFiltersBefore()
{
   // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
   //$orderIds = $objectManager->create('Magento\Customer\Model\Customer')->getCollection()
    	/*$this->addAttributeToSelect('*')
    		->addAttributeToFilter('distributor', array('eq' => 1));*/
    		//$collection = $data;
    	//print_r($collection->getData()); die();
        $this->addFieldToFilter('distributor_request', 1);
    // return $data;
    parent::_renderFiltersBefore();
}

}
