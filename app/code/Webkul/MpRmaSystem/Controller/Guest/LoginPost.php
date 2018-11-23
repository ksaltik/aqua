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

class LoginPost extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @param Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     */
    public function __construct(
        Context $context,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
    ) {
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

        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getParams();
            $orderId = (string) $data['order_id'];
            $orderId = trim($orderId);
            $email = (string) $data['email'];
            $email = trim($email);
            if ($helper->authenticate($orderId, $email)) {
                $helper->loginGuest($email);
                return $this->resultRedirectFactory
                            ->create()
                            ->setPath('*/*/allrma');
            } else {
                $this->messageManager->addError(__('Invalid details for guest.'));
            }
        } else {
            $this->messageManager->addError(__('Something went wrong.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/login');
    }
}
