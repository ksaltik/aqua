<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PurchaseManagement
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PurchaseManagement\Model;

use Webkul\PurchaseManagement\Api\Data\OrderInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Order extends \Magento\Framework\Model\AbstractModel implements OrderInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**#@+
     * Order Status
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * PurchaseManagement Order cache tag
     */
    const CACHE_TAG = 'purchasemanagement_order';

    /**
     * @var string
     */
    protected $_cacheTag = 'purchasemanagement_order';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'purchasemanagement_order';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\PurchaseManagement\Model\ResourceModel\Order');
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteFaq();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Images
     *
     * @return \Webkul\PurchaseManagement\Model\Order
     */
    public function noRouteFaq()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
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
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\PurchaseManagement\Api\Data\OrderInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}