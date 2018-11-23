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

class Update extends \Webkul\MpRmaSystem\Controller\Adminhtml\Rma
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
        if (!$this->getRequest()->getParam('rma_id')) {
            return $this->resultRedirectFactory
                    ->create()
                    ->setPath('*/rma/index');
        }

        $helper = $this->_mpRmaHelper;
        $data = $this->getRequest()->getParams();
        $rmaData = [];
        $finalStatus = 0;
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
        } else {
            $rmaData['status'] = Data::RMA_STATUS_DECLINED;
            $rmaData['final_status'] = Data::FINAL_STATUS_DECLINED;
        }

        $rmaData['seller_status'] = $sellerStatus;
        $rma = $this->_details->create()->load($rmaId);
        $rma->addData($rmaData)->setId($rmaId)->save();
        $helper->sendUpdateRmaEmail($data);
        return $this->resultRedirectFactory
                    ->create()
                    ->setPath(
                        '*/rma/edit',
                        ['id' => $rmaId, 'back' => null, '_current' => true]
                    );
    }
}
