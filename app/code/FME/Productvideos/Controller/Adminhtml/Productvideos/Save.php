<?php

/* ////////////////////////////////////////////////////////////////////////////////
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
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     *
     * @var \Magento\Framework\Controller\Result\RawFactory
     */

    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\ResourceConnection $coreresource)
    {
        parent::__construct($context);
        $this->_coreresource = $coreresource;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $path = $this->_objectManager->get('\Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . "/" . 'productvideoss';
            if (isset($data['video_file'][0]['name']) && isset($data['video_file'][0]['tmp_name'])) {
                $data['video_file'] ='/productvideos/files/'.$data['video_file'][0]['name'];
            } elseif (isset($data['video_file'][0]['name']) && !isset($data['image'][0]['tmp_name'])) {
                $data['video_file'] =$data['video_file'][0]['name'];
            } else {
                $data['video_file'] = null;
            }
            if ($data['video_type'] == 'url') {
                if (empty($data['video_url'])) {
                    $this->messageManager->addError(__('Add A Video Url'));
                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                    $this->_redirect(
                        '*/*/edit',
                        ['id' => $this->getRequest()->getParam('video_id')]
                    );
                    return;
                }
            }
            if ($data['youtube_thumb'] == '' || isset($data['video_thumb'][0]['name'])) {
                if (isset($data['video_thumb'][0]['name']) && isset($data['video_thumb'][0]['tmp_name'])) {
                    $data['video_thumb'] ='/productvideos/files/'.$data['video_thumb'][0]['name'];
                } elseif (isset($data['video_thumb'][0]['name']) && !isset($data['image'][0]['tmp_name'])) {
                    $data['video_thumb'] =$data['video_thumb'][0]['name'];
                } else {
                    $data['video_thumb'] = null;
                }
            } else {
                $data['video_thumb'] = $data['youtube_thumb'];
                $data['video_file'] = null;
            }
            if (isset($data["category_products"])) {
                $cat_array = json_decode($data['category_products'], true);
                $pro_array = array_values($cat_array);
                $c=0;
                foreach ($cat_array as $key => $value) {
                    $pro_array[$c] = $key;
                    $c++;
                }

                unset($data['category_products']);
                $data['product_id'] = $pro_array;
            }
            $id = $this->getRequest()->getParam('video_id');
            if (empty($data['video_id'])) {
                $data['video_id'] = null;
            }

            $model = $this->_objectManager->create(
                'FME\Productvideos\Model\Productvideos'
            );
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);

            if ($id) {
                $model->setId($id);
            }

            try {
                if ($model->getCreatedTime() == null || $model->getUpdateTime() == null) {
                    $model->setCreatedTime(date('y-m-d h:i:s'))->setUpdateTime(date('y-m-d h:i:s'));
                } else {
                    $model->setUpdateTime(date('y-m-d h:i:s'));
                }
                $model->save();
                $this->messageManager->addSuccess(
                    __(
                        'Video was successfully saved'
                    )
                );
                $this->_objectManager->get(
                    'Magento\Backend\Model\Session'
                )->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        ['video_id' => $model->getId()]
                    );
                    return;
                }

                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get(
                    'Magento\Backend\Model\Session'
                )->setFormData($data);
                $this->_redirect(
                    '*/*/edit',
                    ['video_id' => $this->getRequest()->getParam('video_id')]
                );
                return;
            }
        }

        $this->messageManager->addError(__('Unable to find Video to save'));
        $this->_redirect('*/*/');
    }
}
