<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Sunarc\Distributed\Helper;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * Webkul Marketplace Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var null|array
     */
    protected $_options;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_product;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var \Magento\Framework\App\Cache\ManagerFactory
     */
    protected $cacheManager;

    /**
     * @param \Magento\Framework\App\Helper\Context        $context
     * @param \Magento\Framework\ObjectManagerInterface    $objectManager
     * @param \Magento\Customer\Model\Session              $customerSession
     * @param CollectionFactory                            $collectionFactory
     * @param HttpContext                                  $httpContext
     * @param \Magento\Catalog\Model\ResourceModel\Product $product
     * @param \Magento\Store\Model\StoreManagerInterface   $storeManager
     * @param \Magento\Directory\Model\Currency            $currency
     * @param \Magento\Framework\Locale\CurrencyInterface  $localeCurrency
     * @param \Magento\Framework\App\Cache\ManagerFactory  $cacheManagerFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        CollectionFactory $collectionFactory,
        HttpContext $httpContext,
        \Magento\Catalog\Model\ResourceModel\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_customerSession = $customerSession;
        $this->_collectionFactory = $collectionFactory;
        $this->httpContext = $httpContext;
        $this->_product = $product;
        parent::__construct($context);
        $this->_currency = $currency;
        $this->_localeCurrency = $localeCurrency;
        $this->_storeManager = $storeManager;
        $this->cacheManager = $cacheManagerFactory;
    }
    const XML_PATH_MODULE_ENABLE = 'distributed/general/enable';

    /**
     * Return Customer id.
     *
     * @return bool|0|1
     */

    public function getConfigModuleEnabled()
    {



        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $extenionEnabled = false;
        $extensionWorkWithStore = array('default'); //work with only splitorderpro store view
        $storeCode = $storeManager->getStore()->getCode();

        /*$isEnabled_gr =  $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE,'gr');
        $isEnabled_br =  $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE,'br');
        $isEnabled_default =  $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE,'default');*/

        


        $isEnabled_gr =  $this->scopeConfig->getValue(self::XML_PATH_MODULE_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeCode);

        if($isEnabled_gr)
        {
            $extenionEnabled = true;
        }



        /*if($isEnabled_gr && $storeCode=='gr')
        {
         

            $extenionEnabled = true;
        }
        if($isEnabled_br && $storeCode=='br')
        {
           

            $extenionEnabled = true;
        }
        if($isEnabled_default && $storeCode=='default')
        {
            

            $extenionEnabled = true;

        }*/
       
        return $extenionEnabled;


    }
}