<?php
/*////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\  FME Productvideos Module  \\\\\\\\\\\\\\\\\\\\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\\\\\\\\\\\\\\\\\\\\\\\\ NOTICE OF LICENSE\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                                                                   ///////
 \\\\\\\ This source file is subject to the Open Software License (OSL 3.0)\\\\\\\
 ///////   that is bundled with this package in the file LICENSE.txt.      ///////
 \\\\\\\   It is also available through the world-wide-web at this URL:    \\\\\\\
 ///////          http://opensource.org/licenses/osl-3.0.php               ///////
 \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
 ///////                      * @category   FME                            ///////
 \\\\\\\                      * @package    FME_Productvideos              \\\\\\\
 ///////    * @author    FME Extensions <support@fmeextensions.com>   ///////
 \\\\\\\                                                                   \\\\\\\
 /////////////////////////////////////////////////////////////////////////////////
 \\* @copyright  Copyright 2015 © fmeextensions.com All right reserved\\\
 /////////////////////////////////////////////////////////////////////////////////
 */
namespace FME\Productvideos\Controller\Adminhtml\Productvideos;

use Magento\Framework\App\Filesystem\DirectoryList;

class Delete extends \FME\Productvideos\Controller\Adminhtml\Productvideos
{
   
    public function execute()
    {
        $mediaDirectory = $this->_objectManager->get(
            'Magento\Framework\Filesystem'
        )->getDirectoryRead(DirectoryList::MEDIA);
        $mediaRootDir = $mediaDirectory->getAbsolutePath();
        if ($this->getRequest()->getParam('video_id') > 0) {
            try {
                $model = $this->_objectManager->create(
                    'FME\Productvideos\Model\Productvideos'
                );
                $model->load($this->getRequest()->getParam('video_id'));

                /*Delete Media*/
                $video_file = $model->getVideoFile();
                
                
                if ($video_file!=null) {
                    unlink($mediaRootDir.$video_file);
                }

                $model->delete();
                     
                $this->messageManager
                    ->addSuccess(
                        __(
                            'Video was successfully deleted'
                        )
                    );
                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect(
                    '*/*/edit',
                    ['video_id' => $this->getRequest()->getParam('video_id')]
                );
            }
        }

        $this->_redirect('*/*/');
    }
}
