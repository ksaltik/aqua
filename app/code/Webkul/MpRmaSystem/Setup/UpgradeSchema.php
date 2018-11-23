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
namespace Webkul\MpRmaSystem\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()
            ->newTable($setup->getTable('marketplace_rma_items'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'rma_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'RMA Id'
            )
            ->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Item Id'
            )
            ->addColumn(
                'reason_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Reason Id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Product Id'
            )
            ->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Quantity'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0.0000'],
                'Price'
            )
            ->setComment('Marketplace RMA Items Table');
        $setup->getConnection()->createTable($table);

        //remove old colums
        $setup->getConnection()->dropColumn(
            $setup->getTable('marketplace_rma_details'),
            'item_id'
        );
        $setup->getConnection()->dropColumn(
            $setup->getTable('marketplace_rma_details'),
            'reason_id'
        );
        $setup->getConnection()->dropColumn(
            $setup->getTable('marketplace_rma_details'),
            'product_id'
        );
        $setup->getConnection()->dropColumn(
            $setup->getTable('marketplace_rma_details'),
            'qty'
        );
        $setup->getConnection()->dropColumn(
            $setup->getTable('marketplace_rma_details'),
            'price'
        );

        $setup->getConnection()->changeColumn(
            $setup->getTable('marketplace_rma_details'),
            'number',
            'number',
            ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false, 'default' => ''],
            'Consignment Number'
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('marketplace_rma_items'),
            'is_qty_returned',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Is Quantity Returned'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('marketplace_rma_details'),
            'refunded_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length'    => '12,4',
                'nullable' => false,
                'comment' => 'Refunded Amount'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('marketplace_rma_details'),
            'memo_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Credit Memo Id'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('marketplace_rma_details'),
            'customer_name',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Customer Name'
            ]
        );

        $setup->getConnection()->changeColumn(
            $setup->getTable('marketplace_rma_conversation'),
            'message',
            'message',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => '20M',
                'nullable' => false,
                'default' => '',
                'comment' => 'Message'
                
            ]
        );

        $this->updateItems();
        $this->updateNames();
        $setup->endSetup();
    }

    public function getObjectManager()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Inserting previous RMA items into rma details table
     */
    private function updateItems()
    {
        $objectManager = $this->getObjectManager();
        $collection = $objectManager->get('Webkul\MpRmaSystem\Model\ResourceModel\Details\Collection');
        foreach ($collection as $item) {
            $qty = $item->getQty();
            $itemId = $item->getItemId();
            $productId = $item->getProductId();
            $price = $item->getPrice();
            $reasonId = $item->getReasonId();
            $data = [];
            $data['rma_id'] = $item->getId();
            $data['item_id'] = $itemId;
            $data['product_id'] = $productId;
            $data['reason_id'] = $reasonId;
            $data['qty'] = $qty;
            $data['price'] = $price;
            $this->insertItem($data);
        }
    }

    /**
     * Update Customer Name in Table
     */
    private function updateNames()
    {
        $objectManager = $this->getObjectManager();
        $collection = $objectManager->get('Webkul\MpRmaSystem\Model\Details')->getCollection();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $orderGridTable = $resource->getTableName('sales_order');
        $sql = 'main_table.order_id = sog.entity_id';
        $fields = ['*'];
        $collection->getSelect()->join(['sog'=> $orderGridTable], $sql, $fields);

        foreach ($collection as $item) {
            $firstName = $item->getCustomerFirstname();
            $lastName = $item->getCustomerLastname();
            $name = trim($firstName." ".$lastName);
            if ($name == "") {
                $name = "Guest";
            }

            $data = [];
            $data['customer_name'] = $name;
            $this->updateName($item, $data);
        }
    }

    private function updateName($item, $data)
    {
        $item->addData($data)->setId($item->getId())->save();
    }

    private function insertItem($data)
    {
        $objectManager = $this->getObjectManager();
        $model = $objectManager->create('Webkul\MpRmaSystem\Model\Items');
        $model->setData($data)->save();
    }
}
