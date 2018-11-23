<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_MultiInventory
 */


namespace Amasty\MultiInventory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Amasty\MultiInventory\Api\Data\CustomerCoordinatesInterface;

class CustomerCoordinates extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_multiinventory_customer_coordinates', CustomerCoordinatesInterface::ID);
    }
}
