<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpFedexShipping
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpFedexShipping\Block;

use Magento\Catalog\Model\Product;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;

/**
 * Webkul MpFedexShipping Product Create Block.
 *
 * @author      Webkul Software
 */
class ManageFedexShipping extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;
    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var string
     */
    protected $_code = 'mpfedex';
    /**
     * @var CollectionFactory
     */
    protected $__countryCollection;
    /**
     * @var \Magento\Framework\Locale\ListsInterface
     */
    protected $_localeLists;
    /**
     * @var \Webkul\MpFedexShipping\Helper\Data
     */
    protected $_currentHelper;
    /**
     * @var Session
     */
    protected $_customerSession;

    protected $_yesNo;

    /**
     * @param \Magento\Catalog\Block\Product\Context             $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
     * @param \Magento\Framework\Locale\ListsInterface           $localeLists
     * @param Product                                            $product
     * @param \Webkul\MpFedexShipping\Helper\Data              $currentHelper
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Locale\ListsInterface $localeLists,
        CollectionFactory $countryCollection,
        Product $product,
        \Webkul\MpFedexShipping\Helper\Data $currentHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        array $data = []
    ) {
        $this->_product = $product;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_localeLists = $localeLists;
        $this->_countryCollection = $countryCollection;
        $this->_currentHelper = $currentHelper;
        $this->_customerSession = $customerSession;
        $this->_yesNo = $yesNo;
        parent::__construct($context, $data);
    }
    /**
     * Prepare global layout.
     *
     * @return $this
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    /**
     * return current customer session.
     *
     * @return \Magento\Customer\Model\Session
     */
    public function _getCustomerData()
    {
        return $this->_customerSession->getCustomer();
    }
     /**
      * Retrieve information from carrier configuration.
      *
      * @param string $field
      *
      * @return void|false|string
      */
    public function getConfigData($field)
    {
        return $this->getHelper()->getConfigData($field);
    }
    /**
     * get current module helper.
     *
     * @return \Webkul\MpFedexShipping\Helper\Data
     */
    public function getHelper()
    {
        return $this->_currentHelper;
    }

    public function yesNoData()
    {
        return $this->_yesNo->toOptionArray();
    }
}
