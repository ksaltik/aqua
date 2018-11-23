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
use Magento\Framework\Controller\Result\JsonFactory;

/**
 *  Webkul Mpsellervideo Mpvideo Checkvideo controller
 */
class Checkvideo extends Action
{
    protected $resultJsonFactory;

    public function __construct(Context $context, JsonFactory $resultJsonFactory)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            if(isset($data['product']) && $data['product']!=''){
                $productVideo = $this->_objectManager->get('FME\Productvideos\Model\Productvideos')->getProductRelatedVideos($data['product']);
                if($productVideo->getSize()){
                    $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                    $path=$storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)."pub/media";
                    $videoData=array();
                    foreach ($productVideo as $video){
                        //echo "<pre>"; print_r($video->getData());exit;
                        $videoData['video_url']=$path.$video['video_file'];
                        $videoData['video_name']=$video['video_file'];
                        $videoData['image_url']=$path.$video['video_thumb'];
                        $videoData['image_name']=$video['video_thumb'];
                        $videoData['video_id']=$video['video_id'];
                        $videoData['video_title']=$video['title'];
                        $videoData['video_content']=$video['content'];
                    }
                    return $resultJson->setData(['success' => $videoData]);
                }
            }
        }
        return $resultJson->setData(['success' => 0]);
    }
}