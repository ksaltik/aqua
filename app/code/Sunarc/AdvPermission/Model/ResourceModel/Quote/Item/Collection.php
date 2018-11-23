<?php

namespace Sunarc\AdvPermission\Model\ResourceModel\Quote\Item;

class Collection extends \Magento\Reports\Model\ResourceModel\Quote\Item\Collection
{
	/**
     * Add data fetched from another database
     *
     * @return $this
     */
    protected function _afterLoad()
    { 

        $items = $this->getItems();
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item->getProductId();
        }
        $productData = $this->getProductData($productIds);
        $orderData = $this->getOrdersData($productIds);

        $availableProductIds = [];
        foreach ($productData as $pData) {
            $availableProductIds[] = $pData['entity_id'];
        }

        foreach ($items as $item) {
            if( in_array($item->getProductId(), $availableProductIds) ){
                $item->setId($item->getProductId());
                $item->setPrice($productData[$item->getProductId()]['price'] * $item->getBaseToGlobalRate());
                $item->setName($productData[$item->getProductId()]['name']);
                $item->setOrders(0);
                if (isset($orderData[$item->getProductId()])) {
                    $item->setOrders($orderData[$item->getProductId()]['orders']);
                }  
            }
        }

        return $this;
    }

}