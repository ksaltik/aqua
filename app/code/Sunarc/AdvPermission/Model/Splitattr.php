<?php
/**
 * Sunarc_AdvPermission extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SunArc Technologies License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://sunarctechnologies.com/end-user-agreement/
 *
 * @category  Sunarc
 * @package   Sunarc_AdvPermission
 * @copyright Copyright (c) 2017
 * @license
 */
namespace Sunarc\AdvPermission\Model;

/**
 * @method \Sunarc\AdvPermission\Model\ResourceModel\Splitattr _getResource()
 * @method \Sunarc\AdvPermission\Model\ResourceModel\Splitattr getResource()
 */
class Splitattr extends \Magento\Framework\Model\AbstractModel implements \Sunarc\AdvPermission\Api\Data\SplitattrInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sunarc_AdvPermission_splitattr';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sunarc_AdvPermission_splitattr';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'splitattr';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Sunarc\AdvPermission\Model\ResourceModel\Splitattr::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Splitattr id
     *
     * @return array
     */
    public function getSplitattrId()
    {
        return $this->getData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::SPLITATTR_ID);
    }

    /**
     * set Splitattr id
     *
     * @param int $splitattrId
     * @return \Sunarc\AdvPermission\Api\Data\SplitattrInterface
     */
    public function setSplitattrId($splitattrId)
    {
        return $this->setData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::SPLITATTR_ID, $splitattrId);
    }

    /**
     * set Select Attribute For Split Order
     *
     * @param mixed $splitOrderAttr
     * @return \Sunarc\AdvPermission\Api\Data\SplitattrInterface
     */
    public function setSplitOrderAttr($splitOrderAttr)
    {
        return $this->setData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::SPLIT_ORDER_ATTR, $splitOrderAttr);
    }

    /**
     * get Select Attribute For Split Order
     *
     * @return string
     */
    public function getSplitOrderAttr()
    {
        return $this->getData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::SPLIT_ORDER_ATTR);
    }

    /**
     * set Priority
     *
     * @param mixed $priority
     * @return \Sunarc\AdvPermission\Api\Data\SplitattrInterface
     */
    public function setPriority($priority)
    {
        return $this->setData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::PRIORITY, $priority);
    }

    /**
     * get Priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->getData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::PRIORITY);
    }

    /**
     * set Attribute Options
     *
     * @param mixed $attrValue
     * @return \Sunarc\AdvPermission\Api\Data\SplitattrInterface
     */
    public function setAttrValue($attrValue)
    {
        return $this->setData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::ATTR_VALUE, $attrValue);
    }

    /**
     * get Attribute Options
     *
     * @return string
     */
    public function getAttrValue()
    {
        return $this->getData(\Sunarc\AdvPermission\Api\Data\SplitattrInterface::ATTR_VALUE);
    }
}
