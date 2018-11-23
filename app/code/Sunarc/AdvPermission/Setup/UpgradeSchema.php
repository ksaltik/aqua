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
namespace Sunarc\AdvPermission\Setup;


use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup,ModuleContextInterface $context){

        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

            if (version_compare($context->getVersion(), '1.0.2') < 0) {

            // Get module table

                $adminRolesTableRoles = $installer->getTable('authorization_role');
                $adminUserTableRoles = $installer->getTable('admin_user');
                $connection->addColumn(
                    $adminRolesTableRoles,
                    'restrict_by_splitattribute',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'Restrict By Splitattribute'
                );
                $connection->addColumn(
                    $adminRolesTableRoles,
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    [],
                    'Website Id'
                );
                $connection->addColumn(
                    $adminRolesTableRoles,
                    'store_ids',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    [],
                    'Store Ids'
                );

                $connection->addColumn(
                    $adminRolesTableRoles,
                    'is_order_restrict_by_scope',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'is_order_restrict_by_scope'
                );

                $connection->addColumn(
                    $adminRolesTableRoles,
                    'is_invoice_restrict_by_scope',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'is_invoice_restrict_by_scope'
                );

                $connection->addColumn(
                    $adminRolesTableRoles,
                    'is_shipment_restrict_by_scope',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'is_shipment_restrict_by_scope'
                );


                $connection->addColumn(
                    $adminRolesTableRoles,
                    'is_creditmemo_restrict_by_scope',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false],
                    'is_creditmemo_restrict_by_scope'
                );


            }

            if (version_compare($context->getVersion(), '1.0.3') < 0) {

                // Get module table
                $adminRolesTableRoles = $installer->getTable('authorization_role');
                $connection->addColumn(
                    $adminRolesTableRoles,
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    [],
                    'Website Id'
                );
            } 

            if (version_compare($context->getVersion(), '1.0.4') < 0) {

                // Get module table
                $bannersliderSlider = $installer->getTable('magestore_bannerslider_slider');
                $connection->addColumn(
                    $bannersliderSlider,
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    [],
                    'Website Id'
                );
            }  

            if (version_compare($context->getVersion(), '1.0.4') < 0) {

                // Get module table
                $bannersliderSlider = $installer->getTable('magestore_bannerslider_banner');
                $connection->addColumn(
                    $bannersliderSlider,
                    'website_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    [],
                    'Website Id'
                );
            }    
    $setup->endSetup();

    }

}
