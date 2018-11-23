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
namespace Webkul\MpRmaSystem\Controller\Adminhtml\Rma;

use Webkul\MpRmaSystem\Helper\Data;

class Refund extends \Webkul\MpRmaSystem\Controller\Adminhtml\Rma
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @var \Webkul\MpRmaSystem\Model\DetailsFactory
     */
    protected $_details;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param \Webkul\MpRmaSystem\Model\DetailsFactory $details
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
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
        $data = $this->getRequest()->getParams();
        $rmaId = $data['rma_id'];
        $negative = 0;
        $totalPrice = 0;
        $productDetails = $helper->getRmaProductDetails($rmaId);
        if ($productDetails->getSize()) {
            foreach ($productDetails as $item) {
                $totalPrice += $helper->getItemFinalPrice($item);
            }
        }

        if ($data['payment_type'] == 2) {
            $negative = $totalPrice - $data['partial_amount'];
        }

        $data = ['rma_id' => $rmaId, 'negative' => $negative];
        $result = $helper->createCreditMemo($data);
        if ($result['error']) {
            $this->messageManager->addError($result['msg']);
        } else {
            $rmaData = [
                        'status' => Data::RMA_STATUS_SOLVED,
                        'seller_status' => Data::SELLER_STATUS_SOLVED,
                        'final_status' => Data::FINAL_STATUS_SOLVED,
                        'refunded_amount' => $totalPrice - $negative,
                        'memo_id' => $result['memo_id'],
                    ];
            $this->messageManager->addSuccess($result['msg']);
            $rma = $this->_details->create()->load($rmaId);
            $orderId = $rma->getOrderId();
            $rma->addData($rmaData)->setId($rmaId)->save();
            $helper->updateMpOrder($orderId, $result['memo_id']);
            $helper->sendUpdateRmaEmail($data);
            $helper->manageStock($rmaId, $productDetails);
            $helper->updateRmaItemQtyStatus($rmaId);
        }

        return $this->resultRedirectFactory
                    ->create()
                    ->setPath(
                        '*/rma/edit',
                        ['id' => $rmaId, 'back' => null, '_current' => true]
                    );
    }
}
