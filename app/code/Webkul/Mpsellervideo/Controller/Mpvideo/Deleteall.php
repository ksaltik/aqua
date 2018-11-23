<?php

namespace Webkul\Mpsellervideo\Controller\Mpvideo;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;

class Deleteall extends Action
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
        $data = $this->getRequest()->getPostValue();
        //echo "<pre>"; print_r($data);exit;
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
        $mediaRootDir = $mediaDirectory->getAbsolutePath();
        if ($this->getRequest()->getParam('videoId') > 0) {
            try {
                $model = $this->_objectManager->create('FME\Productvideos\Model\Productvideos');
                $model->load($this->getRequest()->getParam('videoId'));
                /*Delete Media*/
                $video_file = $model->getVideoFile();
                if ($video_file != null) {
                    unlink($mediaRootDir . $video_file);
                    $model->setVideoFile();
                }
                /*Delete Media*/
                $video_thumb = $model->getVideoThumb();
                if ($video_thumb != null) {
                    unlink($mediaRootDir . $video_thumb);
                    $model->setVideoThumb();
                }
                $model->delete();
                $this->messageManager->addSuccess(__('Video was successfully deleted'));
                return $resultJson->setData(['success' => 1]);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultJson->setData(['success' => 0]);
            }
        }

        $this->_redirect('*/*/');
    }
}
