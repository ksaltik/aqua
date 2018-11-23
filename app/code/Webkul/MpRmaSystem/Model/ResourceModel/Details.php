<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpRmaSystem
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpRmaSystem\Model\ResourceModel;

class Details extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('marketplace_rma_details', 'id');
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Get Product Details of Requested RMA
     *
     * @param int $rmaId
     *
     * @return collection object
     */
    public function getRmaProductDetails($rmaId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->create("Magento\Framework\App\ResourceConnection");
        $collection = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Item\Collection');

        $tableName = $resource->getTableName('marketplace_rma_items');
        $sql = "rma_items.item_id = main_table.item_id ";
        $collection->getSelect()->join(['rma_items' => $tableName], $sql, ['*']);
        $tableName = $resource->getTableName('marketplace_rma_details');
        $sql = " rma_details.id = rma_items.rma_id ";
        $collection->getSelect()->join(['rma_details' => $tableName], $sql, ['order_id']);
        $collection->getSelect()->where("rma_details.id = ".$rmaId);
        $collection->addFilterToMap("qty", "rma_items.qty");
        $collection->addFilterToMap("product_id", "rma_items.product_id");
        return $collection;
    }
}
