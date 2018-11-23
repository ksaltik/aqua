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
use Webkul\MpRmaSystem\Helper\Data;

class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @var PageFactory
     */
    protected $_details;

    /**
     * @param Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param \Webkul\MpRmaSystem\Model\DetailsFactory $details
     */
    public function __construct(
        Context $context,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        \Webkul\MpRmaSystem\Model\DetailsFactory $details
    ) {
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_details = $details;
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

        if (!$helper->isGuestLoggedIn()) {
            return $this->resultRedirectFactory
                        ->create()
                        ->setPath('*/*/login');
        }

        $rmaId = $this->getRequest()->getParam("id");
        if (!$helper->isValidRma(2)) {
            $this->messageManager->addError(__("Invalid Request"));
        } else {
            $rmaData = ['status' => Data::RMA_STATUS_CANCELED, 'final_status' => Data::FINAL_STATUS_CANCELED];
            $rma = $this->_details->create()->load($rmaId);
            $rma->addData($rmaData)->setId($rmaId)->save();
            $this->messageManager->addSuccess(__("RMA request canceled."));
            $helper->sendUpdateRmaEmail(['rma_id' => $rmaId]);
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/allrma');
    }
}
