<?php
/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsellervideo
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpsellervideo\Block;

use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Unserialize\Unserialize;

/**
 * Seller profile collection
 */
class Profile extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Webkul\Mpsellervideo\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $_sellerCollectionFactory;

    /**
     * @var \Webkul\Mpsellervideo to return collection
     */
    protected $_sellerList;

    /**
     * @var $mphelper Webkul\Marketplace\Helper\Data
     */
    protected $mphelper;

    /**
     * @var $jsonHelper Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var $unserialize Magento\Framework\Unserialize\Unserialize
     */
    protected $unserialize;

    /**
     * @var \Webkul\Mpsellervideo\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollection;

    protected $_objectManager;


    /**
     * @param Context           $context
     * @param CollectionFactory $sellerCollectionFactory
     * @param Unserialize       $unserialize
     * @param array             $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $sellerCollectionFactory,
        Unserialize $unserialize,
        \Webkul\Mpsellervideo\Helper\Data $helper,
        \Webkul\Marketplace\Helper\Data $mphelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerCollection,
        array $data = []
    ) {
        $this->_sellerCollectionFactory = $sellerCollectionFactory;
        $this->unserialize = $unserialize;
        $this->helper = $helper;
        $this->mphelper     = $mphelper;
        $this->jsonHelper     = $jsonHelper;
        $this->sellerCollection = $sellerCollection;
        $this->_objectManager = $objectManager;

        parent::__construct($context, $data);
    }

    /**
     * [getCollection get Marketplace Seller collection ].
     *
     * @return [object] [return collection]
     */
    public function getCollection()
    {
        if (!$this->_sellerList) {
            $collection = [];
            $collection = $this->_sellerCollectionFactory->create()
                ->addFieldToFilter(
                    'seller_id',
                    ['eq' => $this->helper->getSellerId()]
                );

            $this->_sellerList = $collection;
        }

        return $this->_sellerList;
    }
    /**
     * [getSellerData get seller video images and settings].
     *
     * @return [array] [return seller video images and settings]
     */
    public function getSellerData()
    {
        $imageSettings = [];
        $imageAttributes = [];
        $videoImagesArray = [];
        $uploadedImages = [];

        $data = $this->sellerCollection->create()
            ->addFieldToFilter(
                'seller_id',
                ['eq' => $this->helper->getSellerId()]
            );
        if (count($data) > 0) {
            foreach ($data as $seller) {
                $videoImages = $seller->getVideoImg();
                $setting = $seller->getVideoSetting();
                if ($setting !== '' && $setting !== null) {
                    $imageSettings = $this->unserialize->unserialize($setting);
                    foreach ($imageSettings as $key) {
                        array_push($imageAttributes, $key);
                    }
                }
                if ($videoImages !== '' && $videoImages !== null) {
                    if (@unserialize($videoImages)) {
                        $videoImagesArray = unserialize($videoImages);
                    } else {
                        $videoImagesArray = explode(',', $videoImages);
                    }
                    // $videoImagesArray = explode(',', $videoImages);
                    $uploadedImages = array_filter($videoImagesArray);
                }

                return [
                    'images' => $uploadedImages,
                    'settings' => $imageAttributes,
                ];
            }
        } else {
            return false;
        }
    }
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'marketplace.feedback.pager'
            )
                ->setCollection(
                    $this->getCollection()
                );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * [getPartnerProfileById get seller data from Marketplace Model Seller].
     *
     * @param  string $value [seller ID]
     * @return [array] [return seller data]
     */
    public function getPartnerProfileById($value = '')
    {
        if ($value) {
            $data = $this->sellerCollection->create()
                ->addFieldToFilter('seller_id', ['eq'=>$value]);
            foreach ($data as $seller) {
                return $seller;
            }
        }
    }

    /**
     * [checkIsPartner used to check current user is seller or not]
     *
     * @return [integer]
     */
    public function checkIsPartner()
    {
        return $this->mphelper->isSeller();
    }

    /**
     * [getSellerId get current user id]
     *
     * @return [integer]
     */
    public function getSellerId()
    {
        return $this->helper->getSellerId();
    }

    /**
     * [getIsSecure check is secure or not]
     *
     * @return [boolean]
     */
    public function getIsSecure()
    {
        return $this->getRequest()->isSecure();
    }

    /**
     * [getMediaUrl get media url]
     *
     * @return [string]
     */
    public function getMediaUrl()
    {
        return $this->getUrl('pub/media/', ['_secure' => $this->getIsSecure()]);
    }

    /**
     * [getJsonHelper get json helper]
     *
     * @return [object]
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }

    public function getProductList()
    {
        $collection = $this->_objectManager->create(
            'Webkul\Marketplace\Model\Product'
        )
            ->getCollection()
            ->addFieldToSelect(
                ['mageproduct_id']
            )
            ->addFieldToFilter(
                'seller_id',
                $this->getSellerId()
            );
        $productArray=array();
        foreach($collection as $productID){
            $productArray[]=$productID['mageproduct_id'];
        }

        $products = $this->_objectManager->create('Magento\Catalog\Model\Product')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('in' => $productArray));
       return $products;
    }
}
