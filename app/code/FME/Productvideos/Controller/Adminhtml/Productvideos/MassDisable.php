<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Productvideos\Controller\Adminhtml\Productvideos;

use FME\Productvideos\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassDelete
 */
class MassDisable extends AbstractMassStatus
{
    /**
     * Field id
     */
    const ID_FIELD = 'video_id';

    /**
     * ResourceModel collection
     *
     * @var string
     */
    protected $collection = 'FME\Productvideos\Model\ResourceModel\Productvideos\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'FME\Productvideos\Model\Productvideos';

    /**
     * Item status
     *
     * @var bool
     */
    protected $status = 0;
}
