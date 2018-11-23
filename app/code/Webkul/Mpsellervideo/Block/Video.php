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

/*Webkul Mpsellervideo Seller Video Block*/

namespace Webkul\Mpsellervideo\Block;

use Magento\Framework\Unserialize\Unserialize;

/**
 * Seller images collection
 */
class Video extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Webkul\Marketplace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollection;

    /**
     * @var $unserialize Magento\Framework\Unserialize\Unserialize
     */
    protected $unserialize;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                 $context
     * @param \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerCollection
     * @param Unserialize                                                      $unserialize
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Marketplace\Helper\Data $helper,
        \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerCollection,
        Unserialize $unserialize
    ) {
        $this->unserialize = $unserialize;
        $this->helper = $helper;
        $this->sellerCollection = $sellerCollection;
        parent::__construct($context);
    }
    /**
     * [getImagesCollection get All seller video images and settings for video]
     *
     * @return [array] [return seller data ]
     */
    public function getImagesCollection()
    {
        $shopUrl = $this->helper->getProfileUrl();
        $imageSettingsArray = [];
        $videoImagesArray = [];
        $allVideoImages = [];
        if (!$shopUrl) {
            $shopUrl = $this->getRequest()->getParam('shop');
        }
        if ($shopUrl) {
            $data = $this->sellerCollection->create()
                ->addFieldToFilter('shop_url', ['eq' => $shopUrl]);
            if (count($data) > 0) {
                foreach ($data as $seller) {
                    $userid = $seller->getSellerId();
                    $videoImages  = $seller->getVideoImg();
                    $imageSettings = "";
                    if ($seller->getVideoSetting()!==""
                        && $seller->getVideoSetting()!==null
                    ) {
                        $imageSettings = $this->unserialize->unserialize(
                            $seller->getVideoSetting()
                        );
                    }

                    if (isset($imageSettings)
                        && $imageSettings !== ""
                        && $imageSettings !== null
                    ) {
                        foreach ($imageSettings as $key) {
                            array_push($imageSettingsArray, $key);
                        }
                    }
                    if (isset($videoImages)
                        && $videoImages !== ''
                        && $videoImages !== null
                    ) {
                        if (@unserialize($videoImages)) {
                            $videoImagesArray = unserialize($videoImages);
                        } else {
                            $videoImagesArray = explode(',', $videoImages);
                        }
                        // $videoImagesArray = explode(',', $videoImages);
                        $allVideoImages   = array_filter($videoImagesArray);
                    }
                    /* return seller array data */
                    return [
                        'seller_id' => $userid,
                        'images'    => $allVideoImages,
                        'settings'  => $imageSettingsArray,
                    ];
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
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
}
