<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sunarc\Distributed\Controller\Adminhtml\Distributor;

class Index extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customers list action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
         if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        $resultPage = $this->resultPageFactory->create();
        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu('Sunarc_Distributed::customer_distributor');
        $resultPage->getConfig()->getTitle()->prepend(__('Distributor'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Distributors'), __('Distributors'));
        $resultPage->addBreadcrumb(__('Manage Distributors'), __('Manage Distributors'));

        $this->_getSession()->unsCustomerData();
        $this->_getSession()->unsCustomerFormData();

        return $resultPage;
    }
}
