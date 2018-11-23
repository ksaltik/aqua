<?php

namespace Webkul\Mpsellervideo\Controller\Mpvideo;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Deletefile extends Action
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
        $mediaDirectory = $this->_objectManager->get(
            'Magento\Framework\Filesystem'
        )->getDirectoryRead(DirectoryList::MEDIA);
        $mediaRootDir = $mediaDirectory->getAbsolutePath();
        if ($this->getRequest()->getParam('videoId') > 0) {
            try {
                $model = $this->_objectManager->create(
                    'FME\Productvideos\Model\Productvideos'
                );
                $model->load($this->getRequest()->getParam('videoId'));
                if($data['deltype']==1){
                    /*Delete Media*/
                    $video_file = $model->getVideoFile();
                    if ($video_file!=null) {
                        unlink($mediaRootDir.$video_file);
                        $video_file = $model->setVideoFile();
                        $this->messageManager
                            ->addSuccess(
                                __(
                                    'Video was successfully deleted'
                                )
                            );
                    }
                }
                if($data['deltype']==2){
                    /*Delete Media*/
                    $video_thumb = $model->getVideoThumb();
                    if ($video_thumb!=null) {
                        unlink($mediaRootDir.$video_thumb);
                        $video_file = $model->setVideoThumb();
                        $this->messageManager
                            ->addSuccess(
                                __(
                                    'Image was successfully deleted'
                                )
                            );
                    }
                }

                $model->save();


               // $this->_redirect('*/*/');
              //  return 1;
                return $resultJson->setData(['success' => 1]);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultJson->setData(['success' => 0]);
               // return 0;
            }
        }

        $this->_redirect('*/*/');
    }
}
