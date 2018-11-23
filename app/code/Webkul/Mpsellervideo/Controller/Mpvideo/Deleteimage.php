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
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Webkul Mpsellervideo Mpvideo controller
 */

class Deleteimage extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $_session;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $_fileSystem;

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
     * @param \Magento\Framework\Filesystem         $filesystem
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\Mpsellervideo\Helper\Data $helper,
        \Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellerCollection
    ) {
        $this->_session = $customerSession;
        $this->_fileSystem = $filesystem;
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

            $deleteImages = $params['file'];
            $newVideoImags = '';
            $sellerId = $this->helper->getSellerId();
            $collection = $this->sellerCollection->create()
                ->addFieldToFilter('seller_id', $sellerId);

            foreach ($collection as $value) {
                if (@unserialize($value->getVideoImg())) {
                    $videoImages = unserialize($value->getVideoImg());
                    $videoImages = array_filter($videoImages);
                    $key = array_search($deleteImages, array_column($videoImages, 'image'));
                } else {
                    $videoImages = explode(',', $value->getVideoImg());
                    $videoImages = array_filter($videoImages);
                    if (in_array($deleteImages, $videoImages)) {
                        $key = array_search($deleteImages, $videoImages);
                    }
                }

                if (isset($key) && $key!==false) {
                    unset($videoImages[$key]);
                }

                $avatarDir = $this->_fileSystem->getDirectoryRead(
                    DirectoryList::MEDIA
                )->getAbsolutePath(
                    'avatar/'.$sellerId.'/'.$deleteImages
                );

                if (is_file($avatarDir)) {
                    $flag = $this->deleteImage($avatarDir);
                }

                $videoImages = array_filter($videoImages);
                $newVideoImags = serialize($videoImages);
                $value->setVideoImg($newVideoImags)->save();

                if (isset($flag)) {
                    $this->getResponse()->representJson(
                        $this->jsonHelper->jsonEncode(__('Image successfully deleted'))
                    );
                } else {
                    $this->getResponse()->representJson(
                        $this->jsonHelper->jsonEncode(__('something went wrong'))
                    );
                }
            }
        } catch (\Exception $e) {
            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode($e->getMessage())
            );
        }
    }

    /**
     * [deleteImage deletes image]
     *
     * @param  [string] $path [contains image path]
     * @return [object|boolean]
     */
    public function deleteImage($path)
    {
        $directory = $this->_fileSystem->getDirectoryWrite(
            DirectoryList::MEDIA
        );
        $result = $directory->delete($directory->getRelativePath($path));
        return $result;
    }
}
