<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\  FME Productvideos Module  \\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    FME_Productvideos              \\\\\\\
 ///////    * @author    FME Extensions <support@fmeextensions.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2015 © fmeextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */

namespace FME\Productvideos\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $installer = $setup;

        $installer->startSetup();
        /**
         * Create table 'Product Videos Table'
         */
        $table = $installer->getConnection()
          ->newTable($installer->getTable('productvideos'))
        ->addColumn(
            'video_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true],
            'Id'
        )
        ->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Title'
        )
        ->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => ''],
            'Content'
        )
        ->addColumn(
            'video_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Video Type'
        )
        ->addColumn(
            'video_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => ''],
            'Video Url'
        )
        ->addColumn(
            'video_file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => ''],
            'Video File'
        )
        ->addColumn(
            'video_thumb',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            'Thumbnail'
        )
        ->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' =>1],
            'Status'
        )
        ->addColumn(
            'created_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => 0],
            'Created Time'
        )
        ->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => 0],
            'Update Time'
        )
        ->addIndex(
            $installer->getIdxName('video_id', ['video_id']),
            ['video_id']
        )
          ->setComment('Productvideos Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'Productvideos Products Table'
         */
        $table = $installer->getConnection()
          ->newTable($installer->getTable('productvideos_products'))
        ->addColumn(
            'productvideos_related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true],
            'Productvideos Related primary key'
        )
        ->addColumn(
            'productvideos_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Video id'
        )
        ->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product id'
        )
        ->addForeignKey(
            $installer->getFkName(
                'FK_PRODUCTVIDEOS_Product_fbk1',
                'video_id',
                'productvideos',
                'video_id'
            ),
            'productvideos_id',
            $installer->getTable('productvideos'),
            'video_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
          ->setComment('Productvideos Product Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'Productvideos Store Table'
         */
        $table = $installer->getConnection()
          ->newTable($installer->getTable('productvideos_store'))
        ->addColumn(
            'productvideos_storerelated_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true],
            'Productvideos Store Related primary key'
        )
        ->addColumn(
            'productvideos_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Video id'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store id'
        )
        ->addForeignKey(
            $installer->getFkName(
                'FK_PRODUCTVIDEOS_Product_fbk2',
                'video_id',
                'productvideos',
                'video_id'
            ),
            'productvideos_id',
            $installer->getTable('productvideos'),
            'video_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
          ->setComment('Productvideos Product Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
