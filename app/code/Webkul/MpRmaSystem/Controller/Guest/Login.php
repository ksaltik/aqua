<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpRmaSystem
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpRmaSystem\Controller\Guest;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

class Login extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @param Context $context
     * @param PageFactory $_resultPageFactory
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     */
    public function __construct(
        Context $context,
        PageFactory $_resultPageFactory,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
    ) {
        $this->_resultPageFactory = $_resultPageFactory;
        $this->_mpRmaHelper = $mpRmaHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->_mpRmaHelper;
        if ($helper->isLoggedIn()) {
            return $this->resultRedirectFactory
                        ->create()
                        ->setPath('*/customer/allrma');
        }

        if ($helper->isGuestLoggedIn()) {
            return $this->resultRedirectFactory
                        ->create()
                        ->setPath('*/*/allrma');
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Guest Login'));
        return $resultPage;
    }
}
