<?php
namespace Webkul\PurchaseManagement\Setup;


use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
		$installer = $setup;

		$installer->startSetup();

		if(version_compare($context->getVersion(), '2.0.2', '<')) {
			$installer->getConnection()->addColumn(
				$installer->getTable( 'wk_pm_purchase_order' ),
				'admin_id',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					'nullable' => false,
					'length' => '12,4',
					'comment' => 'Logged admin',
					'after' => 'supplier_id'
				]
			);
		}

		if(version_compare($context->getVersion(), '2.0.3', '<')) {

			$setup->getConnection()->changeColumn(
                $setup->getTable('wk_pm_purchase_order'),
                'admin_id',
                'website_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => '12,4',
                    'comment' => 'Created By Website'
                ]
            );
		}
		
		$installer->endSetup();
	}
}