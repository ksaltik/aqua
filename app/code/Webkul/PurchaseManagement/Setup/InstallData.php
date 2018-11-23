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
namespace Webkul\PurchaseManagement\Setup;
 
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
 
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
 
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cost_price',
            [
                'type' => 'varchar',
                'label' => 'Cost Price',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'group' => 'Prices',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'visible' => true,
                'user_defined' => true,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'backend' => '',
                'frontend' => '',
                'apply_to'     => 'simple,configurable,bundle',
                'frontend_class'=>'validate-zero-or-greater',
                'note' => 'Webkul PurchaseManagement'
            ]
        );
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'procurement_method',
            [
                'type' => 'int',
                'label' => 'Procurement Method',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'group' => 'Prices',
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'visible' => true,
                'user_defined' => true,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'backend' => '',
                'frontend' => '',
                'source' =>'Webkul\PurchaseManagement\Model\Config\Source\Options',
                'apply_to' => 'simple,configurable,bundle',
                'note' => 'Webkul PurchaseManagement'
            ]
        );
    }
}
