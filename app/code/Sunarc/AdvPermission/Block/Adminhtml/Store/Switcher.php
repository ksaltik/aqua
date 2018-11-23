<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sunarc\AdvPermission\Block\Store;
//use Magento\Backend\Block\Store\Switcher;

/**
 * Store switcher block
 *
 * @api
 * @since 100.0.2
 */
class Switcher extends \Magento\Backend\Block\Store\Switcher
{

    /**
     * @return \Magento\Store\Model\ResourceModel\Website\Collection
     */
    public function getWebsites()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('\Magento\Store\Model\StoreRepository');
        $stores = $model->getList();

        $websites = $this->_storeManager->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) {
            $websites = array_intersect_key($websites, array_flip($websiteIds));
        }
        return $websites;
    }


}
