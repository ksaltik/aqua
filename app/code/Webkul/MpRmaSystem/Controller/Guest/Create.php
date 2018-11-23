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
use Magento\Framework\App\Filesystem\DirectoryList;
use Webkul\MpRmaSystem\Helper\Data;

class Create extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $_orderItem;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Url $url
     * @param \Magento\Customer\Model\Session $session
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param \Webkul\MpRmaSystem\Model\DetailsFactory $details
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param \Magento\Framework\Filesystem $fileSystem
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Url $url,
        \Magento\Customer\Model\Session $session,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        \Webkul\MpRmaSystem\Model\DetailsFactory $details,
        \Webkul\MpRmaSystem\Model\ItemsFactory $items,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Sales\Model\Order\Item $orderItem,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_url = $url;
        $this->_session = $session;
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_details = $details;
        $this->_items = $items;
        $this->_customer = $customer;
        $this->_orderItem = $orderItem;
        $this->_fileSystem = $fileSystem;
        $this->_fileUploader = $fileUploaderFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $helper = $this->_mpRmaHelper;
        if (!$helper->isGuestLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/login');
        }

        if (!$this->getRequest()->isPost()) {
            $this->messageManager->addError(__('Something went wrong.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/allrma');
        }

        $error = false;
        $msg = '';
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
        $rmaData = $this->getRequest()->getParams();
        if (count($rmaData) > 0) {
            try {
                $orderId = $rmaData['order_id'];
                $time = date('Y-m-d H:i:s');
                $allPrices = [];
                $productIds = [];
                $allQtys = $rmaData['total_qty'];
                $allReasons = $rmaData['reason_ids'];
                $productId = 0;
                foreach ($rmaData['item_ids'] as $itemId) {
                    $orderItem = $this->_orderItem->load($itemId);
                    $productId = $orderItem->getProductId();
                    $allPrices[$itemId] = $orderItem->getPrice();
                    $productIds[$itemId] = $productId;
                    $qty = $allQtys[$itemId];
                    if (!$this->_mpRmaHelper->isRmaAllowed($itemId, $orderId, $qty)) {
                        $this->messageManager->addError(__('Quantity not allowed for RMA.'));
                        return $this->resultRedirectFactory->create()->setPath('*/*/newrma');
                    }
                }

                $sellerId = $this->_mpRmaHelper->getSellerIdByProductId($productId);
                $customerId = 0;
                $email = $helper->getGuestEmailId();
                $customerName = "Guest";
                $order = $this->_mpRmaHelper->getOrder($orderId);
                $rmaData['seller_id'] = $sellerId;
                $rmaData['customer_id'] = $customerId;
                $rmaData['customer_email'] = $email;
                $rmaData['customer_name'] = $customerName;
                $rmaData['seller_status'] = Data::SELLER_STATUS_PENDING;
                $rmaData['status'] = Data::RMA_STATUS_PENDING;
                $rmaData['updated_date'] = $time;
                $rmaData['created_date'] = $time;
                $rmaData['order_ref'] = '#'.$order->getIncrementId();
                $numberOfImages = (int) $rmaData['is_checked'];
                if ($rmaData['is_virtual']) {
                    $rmaData['order_status'] = 2;
                }
                if (!$helper->isAllowedImageUpload($numberOfImages)) {
                    $msg = __('Error in image uploading.');
                    $this->messageManager->addError(__($msg));
                    return $this->resultRedirectFactory->create()->setPath('*/*/newrma');
                }

                $model = $this->_details->create();
                $model->setData($rmaData)->save();
                $rmaId = $model->getId();
                $helper->uploadImages($numberOfImages, $rmaId);
                if (!$helper->setItemsData($productIds, $allReasons, $allQtys, $allPrices, $rmaId)) {
                    $model->delete();
                    $this->messageManager->addError(__('There is some problem in generating RMA.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/newrma');
                }

                $helper->setRegistry("rma_id", $rmaId);
                //Start: Send Email After RMA Creation
                $rmaInfo = $rmaData;
                $rmaInfo['rma_id'] = $rmaId;
                $details = [
                            'type' => 0,
                            'name' => $customerName,
                            'rma' => $rmaInfo
                        ];
                $helper->sendNewRmaEmail($details);
                //End: Send Email After RMA Creation
                $this->messageManager->addSuccess(__('New RMA request generated.'));
                $params = ['id' => $rmaId];
                return $this->resultRedirectFactory->create()->setPath('*/*/rma', $params);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('There is some problem in generating RMA.'));
            }
        } else {
            $this->messageManager->addError(__('Something went wrong.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/allrma');
    }
}
