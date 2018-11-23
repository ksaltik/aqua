<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sunarc\AdvPermission\Model;


//use Magento\Store\Model\WebsiteRepository;

/**
 * Information Expert in store websites handling
 */
class WebsiteRepository extends \Magento\Store\Model\WebsiteRepository
{



       public function getList()
    {
        if (!$this->allLoaded) {
            $websites = $this->getAppConfig()->get('scopes', 'websites', []);
            foreach ($websites as $data) {
                $website = $this->factory->create([
                    'data' => $data
                ]);
                 if(in_array($website->getId(), array(1))){
                $this->entities[$website->getCode()] = $website;
                $this->entitiesById[$website->getId()] = $website;}
            }
            $this->allLoaded = true;
        }
        return $this->entities;
    }


}
