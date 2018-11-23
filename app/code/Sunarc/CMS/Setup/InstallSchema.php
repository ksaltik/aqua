<?php
/**
 * Copyright © 2015 Sunarc. All rights reserved.
 */

namespace Sunarc\CMS\Setup;

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
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'cms_sunarccms'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('cms_sunarccms')
        )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'cms_sunarccms'
            )
            ->addColumn(
                'theme_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'theme_id'
            )
            ->addColumn(
                'cms_page',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'cms_page'
            )
            ->addColumn(
                'block_details',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'block_details'
            )
            ->addColumn(
                'mobile_details',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'mobile_details'
            )
        /*{{CedAddTableColumn}}}*/
        
        
            ->setComment(
                'Sunarc CMS cms_sunarccms'
            );
        
        $installer->getConnection()->createTable($table);
        /*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
