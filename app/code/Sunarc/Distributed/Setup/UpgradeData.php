<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sunarc\Distributed\Setup;

use Magento\Customer\Model\Customer;
use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\SetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\FieldDataConverterFactory;
use Magento\Framework\DB\DataConverter\SerializedToJson;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpgradeData implements UpgradeDataInterface
{
    private $customerSetupFactory;
    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $installer = $setup;
            $installer->startSetup();
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $entityTypeId = $customerSetup->getEntityTypeId(\Magento\Customer\Model\Customer::ENTITY);

            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "distributor_request",  array(
                "type"     => "int",
                "backend"  => "",
                "label"    => "Distributor Request",
                "input"    => "boolean",
                "source"   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                "visible"  => true,
                "required" => false,
                "default" => "",
                "frontend" => "",
                "unique"     => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true,
                'user_defined' => true, //commented because causing attribute fail on module install
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'default' => '0',
                'unique' => 0,
                "note"       => ""

            ));

            $distributor   = $customerSetup->getAttribute(\Magento\Customer\Model\Customer::ENTITY, "distributor_request");

            $distributor = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'distributor_request');
            $used_in_forms[]="adminhtml_customer";
            $used_in_forms[]="checkout_register";
            $used_in_forms[]="customer_account_create";
            $used_in_forms[]="customer_account_edit";
            $used_in_forms[]="adminhtml_checkout";
            $distributor->setData("used_in_forms", $used_in_forms)
                ->setData("is_used_for_customer_segment", true)
                ->setData("is_system", 0)
                ->setData("is_user_defined", 1)
                ->setData("is_visible", 1)
                ->setData("sort_order", 100);
            $distributor->save();
        }




        $setup->endSetup();
    }


}
