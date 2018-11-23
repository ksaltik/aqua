<?php
/**
 * Copyright © 2015 Sunarc. All rights reserved.
 */
namespace Sunarc\CMS\Model\ResourceModel;

/**
 * Sunarccms resource
 */
class Sunarccms extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('cms_sunarccms', 'id');
    }

  
}
