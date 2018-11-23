<?php
/**
 * Sunarc_AdvPermission
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * Sunarc_AdvPermission
 *
 * @category Sunarc_AdvPermission
 * @package Sunarc_AdvPermission
 * @copyright Copyright (c) 2014 Zowta LLC (http://www.sunarctechnologies.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Sunarc_AdvPermission Team support@sunarctechnologies.com
 *
 */

namespace Sunarc\AdvPermission\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Sunarc\AdvPermission\Model\ResourceModel\Splitattr\CollectionFactory;

class Data extends AbstractHelper
{

    const CONFIG_PATH_MODULE_ENABLED = 'advsplitorder/general/enable';
    const CONFIG_PATH_CONDITION_ENABLED = 'advsplitorder/general/selectoption';


    protected $_objectManager;
    protected $scopeConfig;
    protected $eavConfig;
    protected $block;
    protected $splitattrCollectionFactory;
    protected $checkoutSession;
    protected $authSession;
    protected $orderCollectionFactory;
    protected $userFactory;
    protected $storeManager;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\User\Model\UserFactory $userFactory,
        CollectionFactory $splitattrCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    

        $this->_objectManager = $objectmanager;
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->eavConfig = $eavConfig;
        $this->authSession = $authSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->userFactory = $userFactory;
        $this->splitattrCollectionFactory = $splitattrCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /*
     * check if module enabled
     */
     public function getConfigModuleEnabled()
     {
         return $this->scopeConfig->getValue(self::CONFIG_PATH_MODULE_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
     }
    

    /*
     * check enabled conditions
     */
    public function getConditionEnabled()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_CONDITION_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * used if order splits on the basis of 'split order if attribute exists'
     * Check the attribute exist in order or not.
     * @access public
     *
     */

    public function checkAttributeExist()
    {
        if ($this->getsplitorderCollection()!=null) {
            list($splitAttribute, $attrOptionArray) = $this->getsplitorderCollection();
            $attributeData = [];
            $attributeExist = 0;
            $splitAttributeCode = $attributeCode = $this->getSplitAttrCode($splitAttribute);
            foreach ($this->checkoutSession->getQuote()->getAllItems() as $item) {
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());
                $attr = $product->getResource()->getAttribute($splitAttributeCode);
                $optText = $product->getAttributeText($splitAttributeCode);
                $attributeData[] = $attr->getSource()->getOptionId($optText);
            }
            $count = count(array_intersect($attrOptionArray, $attributeData));
            if ($count) {
                $attributeExist = 1;
            }
            return $attributeExist;
        } else {
            return 0;
        }
    }

    /**
     * used if order splits on the basis of 'split order on basis of attribute'
     * Return Product Ids on the basis of their attributes
     * Check the attribute exist in order or not.
     * @access public
     * @return array
     */
    public function splitAttribute()
    {
        $optionText = [];
        $productIDs = [];
        if ($this->getsplitorderCollection()!=null) {
            list($splitAttribute, $attrOptionArray) = $this->getsplitorderCollection();
            $attributeCode = $this->getSplitAttrCode($splitAttribute);
            foreach ($this->checkoutSession->getQuote()->getAllItems() as $item) {
                //get productId's array getting option value from each product
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());
                if ($product->getTypeId()=='virtual') {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $product_new = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($item->getProduct()->getId());
                    $productIDs[$product->getAttributeText($attributeCode)][] = $product_new[0];
                } elseif ($product->getTypeId()!='configurable') {
                    $productIDs[$product->getAttributeText($attributeCode)][] = $item->getProduct()->getId();
                }
            }

            return $productIDs;
        } else {
            $new_array= [];
            return $new_array;
        }
    }

    /**
     * used if order splits on the basis of 'split order on basis of attribute'
     * Return Attribute Ids of selected attribute from split order by attribute grid
     * @access public
     * @return array
     */
    public function getSplitAttrId()
    {
        //get attribute ids by which order will split
        list($splitAttribute, $attrOptionArray) = $this->getsplitorderCollection();
        return $splitAttribute;
    }

    /**
     * used if order splits on the basis of 'split order on basis of attribute'
     * Return split order attribute options array based on selected attribute from split order by attribute grid
     * @access public
     * @return array
     */
    public function splitoptionArr()
    {
        list($splitAttribute, $attrOptionArray) = $this->getsplitorderCollection();
        $attributeCode = $this->getSplitAttrCode($splitAttribute);
        $entityType = 'catalog_product';
        //get attribute details from attribute code
        $attributeDetails = $this->eavConfig->getAttribute("catalog_product", $attributeCode);
        foreach ($attrOptionArray as $optionId) {
            //get attribute option's label from option id
            $optionText[] = $attributeDetails->getSource()->getOptionText($optionId);
        }

        return $optionText;
    }

    public function getsplitorderCollection()
    {
        $storeData = [];
        $splitFactory = $this->splitattrCollectionFactory->create()->addFieldToSelect('split_order_attr')
            ->addFieldToSelect('attr_value')
            ->setOrder('priority', 'ASC')
            ->setOrder('updated_at', 'DESC')
            ->getFirstItem();
        $storeData = $splitFactory->getData();
        if (!(empty($storeData))) {
            $latestSplitAttr = $splitFactory->getFirstItem();
            $attributeOptions = $storeData['attr_value'];
            $attrOptionArray = explode(',', $attributeOptions);
            $splitAttribute = $storeData['split_order_attr'];
            return [$splitAttribute, $attrOptionArray];
        } else {
            return null;
        }
    }

    /**
     * @param $splitAttribute
     * @return mixed
     */
    public function getSplitAttrCode($splitAttribute)
    {
        $eavModel = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
        $attr = $eavModel->load($splitAttribute);
        $attributeCode = $eavModel->getAttributeCode();
        return $attributeCode;
    }

    /**
     * @return bool
     * functions for admin role based access
     *
     */
    public function canRestrictBySplitAttribute($user)
    {
        $userId = $user->getUserId();
        $userDetails = $this->userFactory->create()->load($userId);
        $role = $userDetails->getRole();
        if ($role && $role->getRestrictBySplitattribute()) {
            return true;
        }
        return false;
    }

    /*Function for get order item split attribute code and value */
    public function getOrderSplitAttributeValue()
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToSelect(['split_attribute_value' => new Zend_Db_Expr('group_concat(`main_table`.split_attribute_value SEPARATOR ",")')])
            ->addFieldToSelect('split_attribute_code')
            ->addFieldToFilter('split_attribute_value', ['neq' => null]);
        $collection->getSelect()->group('main_table.split_attribute_code');

        foreach ($collection as $key => $value) {
            $splitAttributeCode[] = $value['split_attribute_code'];
            $splitAttributeValue[] = explode(',', $value['split_attribute_value']);
        }

        return [$splitAttributeCode, $splitAttributeValue];
    }

    /*Function for get order split attribute option label*/
    public function getSplitOptionValue($attributes, $attrOptionArray)
    {
        $count = 0;
        foreach ($attributes as $attribute) {
            foreach ($attrOptionArray[$count] as $attributeOptions) {
                $countOptions = 0;
                $optionText[$attributeOptions] = $attribute->getSource()->getOptionText($attributeOptions);
                ++$countOptions;
            }

            ++$count;
        }

        return $optionText;
    }

    /*Function for get split product attribute value for restriction on order in admin user form*/
    public function getProductAttributeValuesForForm()
    {
        $optionsValue = [];
        list($splitAttribute, $attrOptionArray) = $this->getsplitorderCollection();
        $attributeCode = $this->getSplitAttrCode($splitAttribute);
        $attributeDetails = $this->eavConfig->getAttribute("catalog_product", $attributeCode);
        foreach ($attrOptionArray as $optionId) {
            $optionsValue[] = [
                'value' => $optionId,
                'label' => $attributeDetails->getSource()->getOptionText($optionId),
            ];
        }
        return $optionsValue;
    }

    //    function for get restrict order for admin user sales grid
    public function getRestrictOrderCollection($splitAttributeRestrictions)
    {
        $orderIds = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderCollection = $objectManager->get('Magento\Sales\Model\Order\Item')->getCollection();
        $orderCollection->addFieldToSelect('order_id')
            ->addFieldToFilter('split_attribute_value', ['in' => $splitAttributeRestrictions]);
        foreach ($orderCollection as $item) {
            $orderIds[] = $item->getOrderId();
        }

        return $orderIds;
    }

    //    function for get restrict order for admin user sales grid
    public function getRestrictBlockCollection()
    {
        $orderIds = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderCollection = $objectManager->get('Magento\Cms\Model\Block\Item')->getCollection();
        $orderCollection->addFieldToSelect('block_id');
        foreach ($orderCollection as $item) {
            $orderIds[] = $item->getBlockId();
        }

        return $orderIds;
    }


    public function printLog($data, $flag = 1, $filename = "adminrestrict.log")
    {
        if ($flag == 1) {
            $path = $this->_directoryList->getPath("var");
            $logger = new \Zend\Log\Logger();
            if (!file_exists($path . "/log/"))
                mkdir($path . "/log/", 0777, true);
            $logger->addWriter(new \Zend\Log\Writer\Stream($path . "/log/" . $filename));
            if (is_array($data) || is_object($data))
                $data = print_r($data, true);
            $logger->info($data);
        }
    }


    public function getScopeValuesForForm()
    {
        $optionsValue = array();
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $registry = $_objectManager->get('Magento\Framework\Registry');
        $user = $this->authSession->getUser();
        $userId = $user->getUserId();
        $userDetails = $this->userFactory->create()->load($userId);
        $role = $userDetails->getRole();

        if ($role && $role->getStoreIds()) {
            $optionsValue = explode(',',$role->getStoreIds());
            return $optionsValue;
        }

        return $optionsValue;
    }

    public function getScopeValuesForWebsite()
    {
        $optionsValue = array();
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $registry = $_objectManager->get('Magento\Framework\Registry');
        $user = $this->authSession->getUser();
        $userId = $user->getUserId();
        $userDetails = $this->userFactory->create()->load($userId);
        $role = $userDetails->getRole();
        $websiteIds = [];
        
        if ($role && $role->getStoreIds()) {
            $optionsValue = explode(',',$role->getStoreIds());
        }

        if ( count($optionsValue) > 0 ) {
            foreach ($optionsValue as $storeId) {
                $websiteIds[] = $this->storeManager->getStore($storeId)->getWebsiteId();                
            }
        }
        return array_unique($websiteIds);
    }

    public function getRestrictOrderCollectionByScope()
    {
        $orderIds = [];
        $storeIds = $this->getScopeValuesForForm();

        $websiteIds = $this->getScopeValuesForWebsite();
        if(isset($storeIds) && $storeIds && !empty($websiteIds)) {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderCollection = $objectManager->get('Magento\Sales\Model\Order\Item')->getCollection();
            $orderCollection->addFieldToSelect('order_id')
                ->addFieldToFilter('store_id', ['in' => $storeIds]);
            foreach ($orderCollection as $item) {
                $orderIds[] = $item->getOrderId();
            }
        }    

        return $orderIds;
    }

    public function getRestrictBlockCollectionByScope($collection = null)
    {
        $orderIds = [];
        $storeIds = $this->getScopeValuesForForm();
        $websiteIds = $this->getScopeValuesForWebsite();
        if(isset($storeIds) && $storeIds && !empty($websiteIds)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderCollection = $objectManager->get('Magento\Cms\Model\ResourceModel\Block\CollectionFactory')->create();

            $orderCollection = $orderCollection->join(array('block_store' => 'cms_block_store'), 'main_table.block_id = block_store.block_id', array('store_id'));
            $orderCollection->addFieldToFilter('store_id', ['in' => [$storeIds , '0'] ]);
           
            foreach ($orderCollection as $item) {
                $orderIds[] = $item->getBlockId();
            }
        }
        return $orderIds;
    }


    /* get cms pages according to the user-store scope */
    function getRestrictPageCollectionByScope(){
        $orderIds = [];
        $storeIds = $this->getScopeValuesForForm();
        $websiteIds = $this->getScopeValuesForWebsite();
        if(isset($storeIds) && $storeIds && !empty($websiteIds)) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderCollection = $objectManager->get('Magento\Cms\Model\ResourceModel\Page\CollectionFactory')->create();

            $orderCollection = $orderCollection->join(array('page_store' => 'cms_page_store'), 'main_table.page_id = page_store.page_id', array('store_id'));
            $orderCollection->addFieldToFilter('store_id', ['in' => [$storeIds , '0'] ]);
            foreach ($orderCollection as $item) {
                $orderIds[] = $item->getPageId();
            }
        }
        return $orderIds;
    }

    /* get customers according to the user-website scope */
    public function getRestrictCustomerCollectionByScope()
    {
        $customerIds = [];
        $websiteIds = $this->getScopeValuesForWebsite();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerCollection = $objectManager->get('Magento\Customer\Model\Customer')->getCollection();

        if(!empty($websiteIds)) {
            $customerCollection->addFieldToSelect('entity_id')
                ->addFieldToFilter('website_id', ['in' => [$websiteIds] ]);
        }

        foreach ($customerCollection as $item) {
            $customerIds[] = $item->getEntityId();
        }
        return $customerIds;
    }

}
