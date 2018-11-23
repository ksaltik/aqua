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

class Edit extends \FME\Productvideos\Controller\Adminhtml\Productvideos
{


    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /**
 * @var \Magento\Backend\Model\View\Result\Page $resultPage
*/
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Productvideos::productvideos')
            ->addBreadcrumb(__('Productvideos'), __('Productvideos'))
            ->addBreadcrumb(__('Manage Videos'), __('Manage Videos'));
        return $resultPage;
    }

    
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $id     = $this->getRequest()->getParam('video_id');
        $model  = $this->_objectManager->create(
            'FME\Productvideos\Model\Productvideos'
        )->load($id);
        if ($model->getId() || $id == 0) {
            $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            $this->_objectManager->get('Magento\Framework\Registry')->register('productvideos_data', $model);
            $resultPage = $this->_initAction();
            $resultPage->addBreadcrumb(
                $id ? __('Edit Videos') : __('New Videos'),
                $id ? __('Edit Videos') : __('New Videos')
            );
            $resultPage->getConfig()->getTitle()->prepend(__('Productvideos'));
            $resultPage->getConfig()->getTitle()
                ->prepend($model->getVideoId() ? $model->getTitle() : __('New Videos'));

            return $resultPage;
        } else {
            $this->messageManager->addError(__('Videos does not exist'));
            $this->_redirect('*/*/');
        }
    }
}
