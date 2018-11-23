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

namespace Webkul\Mpsellervideo\Controller\Mpvideo;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Session;

/**
 * Webkul Mpsellervideo Mpvideo controller
 */

class Updateimage extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_session;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Webkul\Mpsellervideo\Helper\Data
     */
    protected $helper;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollection;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param Session                               $customerSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\Mpsellervideo\Helper\Data $helper,
        \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerCollection
    ) {
        $this->_session = $customerSession;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->sellerCollection = $sellerCollection;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param  RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get(
            'Magento\Customer\Model\Url'
        )->getLoginUrl();

        if (!$this->_session->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * delete images through ajax call from DB
     * and pub/media/avatar/seller_id directory.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();

            $imageName = $params['file'];
            $imageUrl = $params['img_url'];
            $sellerId = $this->helper->getSellerId();
            $collection = $this->sellerCollection->create()
                ->addFieldToFilter('seller_id', $sellerId);

            foreach ($collection as $value) {
                $getImages = $value->getVideoImg();
                if (@unserialize($getImages)) {
                    $videoImages = unserialize($getImages);
                    $videoImages = array_filter($videoImages);
                    $key = array_search($imageName, array_column($videoImages, 'image'));
                    if ($key!==false) {
                        $videoImages[$key]['url'] = $imageUrl;
                    }
                } else {
                    $videoImages = explode(',', $getImages);
                    $videoImages = array_filter($videoImages);
                    if (in_array($imageName, $videoImages)) {
                        $key = array_search($imageName, $videoImages);
                    }
                    if (isset($key) && $key!==false) {
                        foreach ($videoImages as $newkey => $newImageFormat) {
                            $videoImages[$newkey] = [];
                            if ($newkey == $key) {
                                $newImgUrl = $imageUrl;
                            } else {
                                $newImgUrl = '#';
                            }
                            $videoImages[$newkey]['image'] = $newImageFormat;
                            $videoImages[$newkey]['url'] = $newImgUrl;
                        }
                    }
                }

                $videoImages = array_filter($videoImages);
                $newVideoImags = serialize($videoImages);
                $value->setVideoImg($newVideoImags)->save();

                $this->getResponse()->representJson(
                    $this->jsonHelper->jsonEncode(__('Image Url successfully updated'))
                );
            }
        } catch (\Exception $e) {
            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode($e->getMessage())
            );
        }
    }
}
