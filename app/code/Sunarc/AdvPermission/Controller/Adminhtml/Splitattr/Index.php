<?php
/**
 * Sunarc_AdvPermission extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SunArc Technologies License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://sunarctechnologies.com/end-user-agreement/
 *
 * @category  Sunarc
 * @package   Sunarc_AdvPermission
 * @copyright Copyright (c) 2017
 * @license
 */
namespace Sunarc\AdvPermission\Controller\Adminhtml\Splitattr;

class Index extends \Sunarc\AdvPermission\Controller\Adminhtml\Splitattr
{
    /**
     * Splitattrs list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sunarc_AdvPermission::splitattr');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Attribute'));
        $resultPage->addBreadcrumb(__('Splitorder'), __('Splitorder'));
        $resultPage->addBreadcrumb(__('Splitattrs'), __('Manage Attribute'));
        return $resultPage;
    }
}
