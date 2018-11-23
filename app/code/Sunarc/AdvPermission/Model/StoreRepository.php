<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sunarc\AdvPermission\Model;

//use Magento\Store\Model\StoreRepository;

/**
 * Information Expert in stores handling
 */
class StoreRepository extends \Magento\Store\Model\StoreRepository
{


    public function getList()
    {
        if ($this->allLoaded) {
            return $this->entities;
        }
        $stores = $this->getAppConfig()->get('scopes', "stores", []);
        foreach ($stores as $data) {            
            $store = $this->storeFactory->create([
                'data' => $data
            ]);
           // if(in_array($store->getId(), array(1))){
            $this->entities[$store->getCode()] = $store;
            $this->entitiesById[$store->getId()] = $store;//}
        }
        $this->allLoaded = true;
        return $this->entities;
    }


}
