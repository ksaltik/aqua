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
namespace Webkul\MpRmaSystem\Controller\Rma;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Webkul\MpRmaSystem\Helper\Data;

class Refund extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @var \Webkul\MpRmaSystem\Model\DetailsFactory
     */
    protected $_details;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Url $url
     * @param \Magento\Customer\Model\Session $session
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param \Webkul\MpRmaSystem\Model\DetailsFactory $details
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Url $url,
        \Magento\Customer\Model\Session $session,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        \Webkul\MpRmaSystem\Model\DetailsFactory $details
    ) {
        $this->_url = $url;
        $this->_session = $session;
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_details = $details;
        parent::__construct($context);
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_url->getLoginUrl();
        if (!$this->_session->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
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
                        '*/seller/rma',
                        ['id' => $rmaId, 'back' => null, '_current' => true]
                    );
    }
}
