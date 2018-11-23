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

class Change extends \Magento\Framework\App\Action\Action
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
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory
                    ->create()
                    ->setPath('*/seller/allrma');
        }

        $helper = $this->_mpRmaHelper;
        $data = $this->getRequest()->getParams();
        $rmaData = [];
        $finalStatus = 0;
        $returnQty = false;
        $rmaId = $data['rma_id'];
        $sellerStatus = $data['seller_status'];
        if ($sellerStatus == Data::SELLER_STATUS_PENDING || $sellerStatus == Data::SELLER_STATUS_PACKAGE_NOT_RECEIVED) {
            $rmaData['status'] = Data::RMA_STATUS_PENDING;
        } elseif ($sellerStatus == Data::SELLER_STATUS_PACKAGE_RECEIVED) {
            $rmaData['status'] = Data::RMA_STATUS_PROCESSING;
        } elseif ($sellerStatus == Data::SELLER_STATUS_PACKAGE_DISPATCHED) {
            $rmaData['status'] = Data::RMA_STATUS_PROCESSING;
        } elseif ($sellerStatus == Data::SELLER_STATUS_SOLVED) {
            $rmaData['status'] = Data::RMA_STATUS_SOLVED;
        } elseif ($sellerStatus == Data::SELLER_STATUS_ITEM_CANCELED) {
            $rmaData['status'] = Data::RMA_STATUS_SOLVED;
            $rmaData['final_status'] = Data::FINAL_STATUS_SOLVED;
            $returnQty = true;
        } else {
            $rmaData['status'] = Data::RMA_STATUS_DECLINED;
            $rmaData['final_status'] = Data::FINAL_STATUS_DECLINED;
        }

        $rmaData['seller_status'] = $sellerStatus;
        $rma = $this->_details->create()->load($rmaId);
        $rma->addData($rmaData)->setId($rmaId)->save();
        $helper->sendUpdateRmaEmail($data);
        if ($returnQty) {
            $helper->processCancellation($rmaId);
            $helper->updateRmaItemQtyStatus($rmaId);
            $helper->cancelOrder($rmaId);            
        }
        return $this->resultRedirectFactory
                    ->create()
                    ->setPath(
                        '*/seller/rma',
                        ['id' => $rmaId, 'back' => null, '_current' => true]
                    );
    }
}
