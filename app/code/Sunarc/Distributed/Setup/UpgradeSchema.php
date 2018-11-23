<?php

namespace Sunarc\Distributed\Setup;


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

                 $installer->getConnection()->addColumn(
                    $setup->getTable('customer_entity'),
                'distributor_request',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Request For Distributor'
                ]
            );
              

        }
    $setup->endSetup();

    }

}
