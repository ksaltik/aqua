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
namespace Webkul\MpRmaSystem\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollection;

class Items extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Webkul\MpRmaSystem\Helper\Data
     */
    protected $_mpRmaHelper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Undocumented function
     *
     * @param Context $context
     * @param \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper
     * @param \Magento\Sales\Model\OrderFactory $order
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(
        Context $context,
        \Webkul\MpRmaSystem\Helper\Data $mpRmaHelper,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Webkul\Marketplace\Model\OrdersFactory $mpOrder,
        InvoiceCollection $invoiceCollection,
        ShipmentCollection $shipmentCollection
    ) {
        $this->_mpRmaHelper = $mpRmaHelper;
        $this->_order = $order;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_imageHelper = $imageHelper;
        $this->_mpOrder = $mpOrder;
        $this->_invoiceCollection = $invoiceCollection;
        $this->_shipmentCollection = $shipmentCollection;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resolutions = [];
        $orderDetails = [];
        $error = false;
        $helper = $this->_mpRmaHelper;
        $data = $this->_request->getParams();
        $sellers = [];
        if (array_key_exists("is_guest", $data)) {
            $isGuest = $data['is_guest'];
            if ($this->isBuyerLoggedIn($isGuest)) {
                $orderId = $this->_request->getParam('order_id');
                $order = $this->_order->create()->load($orderId);
                $orderedItems = $order->getAllVisibleItems();
                $orderStatus = $order->getStatus();
                $type = 1;
                if ($orderStatus == "pending") {
                    $type = 0;
                }

                $info = ['isLoggedIn' => 1, 'items' => []];
                $sellerDetails = [];
                foreach ($orderedItems as $item) {
                    $itemId = $item->getId();
                    $product = $item->getProduct();
                    $productId = $product->getId();
                    $details = $this->_mpRmaHelper->getSellerDetailsByProductId($productId);
                    $sellerId = $details['seller_id'];
                    $orderDetails[$sellerId][] = $itemId;
                    $sellerDetails[$item->getId()] = $details;
                }

                $orderDetails = $this->getStatusDetails($orderDetails);
                if (count($orderDetails) > 1) {
                    $info['multi_seller'] = 1;
                } else {
                    $info['multi_seller'] = 0;
                    foreach ($orderDetails as $sellerId => $statusData) {
                        $info['order_status'] = $statusData['order_status'];
                        $info['shipment_status'] = $statusData['shipment_status'];
                    }
                }

                $totalQty = 0;
                foreach ($orderedItems as $item) {
                    $product = $item->getProduct();
                    $imageUrl = $this->_imageHelper
                                    ->init($product, 'product_page_image_small')
                                    ->setImageFile($product->getImage())
                                    ->keepAspectRatio(true)
                                    ->resize(100, 100)
                                    ->getUrl();
                    $itemId = $item->getId();
                    $productId = $product->getId();
                    $originalQty = $item->getQtyOrdered();
                    $sku = $item->getSku();
                    $price = $item->getPrice();
                    $name = $item->getName();
                    $url = $product->getProductUrl();
                    $details = $sellerDetails[$itemId];
                    $sellerId = $details['seller_id'];
                    $sellers[$sellerId] = $details['seller_name'];
                    $orderStatus = $orderDetails[$sellerId]['order_status'];
                    $shipmentStatus = $this->getShipmentStatus($orderId);
                    $qty = $helper->getRmaQty($itemId, $orderId, $originalQty, $orderStatus);

                    $arr = [
                            'is_virtual' => (int) $item->getProduct()->getIsVirtual(),
                            'product_url' => $url,
                            'product_image' => $imageUrl,
                            'price' => $order->formatPrice($item->getPrice()),
                            'sku' => $sku,
                            'name' => $name,
                            'qty' => $qty,
                            'original_qty' => $originalQty,
                            'id' => $productId,
                            'item_id' => $itemId,
                            'itemId' => $itemId,
                            'productUrl' => $url,
                            'productImage' => $imageUrl,
                            "optionHtml" => $this->getOptionsHtml($item)
                        ];

                    if ($qty = $helper->getAvailableRmaQty($itemId, $orderId, $originalQty, 2)) {
                        $arr['qty'] = $qty;
                        $info['items'][$sellerId][1][] = $arr;
                        $info['items'][$sellerId][2][] = $arr;
                        $resolutions[$sellerId][1] = __("Refund");
                        $resolutions[$sellerId][2] = __("Replace");
                        $totalQty += $qty;
                    }

                    if ($qty = $helper->getAvailableRmaQty($itemId, $orderId, $originalQty, 3)) {
                        $arr['qty'] = $qty;
                        $info['items'][$sellerId][3][] = $arr;
                        $resolutions[$sellerId][3] = __("Cancel Items");
                        $totalQty += $qty;
                    }
                }
            } else {
                $info = ['isLoggedIn' => 0];
            }
        } else {
            $info = ['isLoggedIn' => 0];
        }

        $info["total_qty"] = $totalQty;
        $info["sellers"] = $sellers;
        $info["order_details"] = $orderDetails;
        $info["resolutions"] = $resolutions;
        if (isset($data['dev'])) {
            echo "<pre>";
            print_r($info);
            die;
        }
        $result = $this->_resultJsonFactory->create();
        $result->setData($info);
        return $result;
    }

    public function isBuyerLoggedIn($isGuest)
    {
        $helper = $this->_mpRmaHelper;
        if ($isGuest == 1) {
            if (!$helper->isGuestLoggedIn()) {
                return false;
            }
        } else {
            if (!$helper->isLoggedIn()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get Order Item Option Html
     *
     * @param object $orderItem
     *
     * @return html
     */
    public function getOptionsHtml($orderItem)
    {
        return $this->_mpRmaHelper->getOptionsHtml($orderItem);
    }
    
    /**
     * Get Shipment Status
     *
     * @param int $orderId
     *
     * @return void
     */
    public function getShipmentStatus($orderId)
    {
        $status = 0;
        $collection = $this->_shipmentCollection
                            ->create()
                            ->addFieldToFilter("order_id", $orderId);
        if ($collection->getSize()) {
            $status = 1;
        }

        return $status;
    }

    public function getStatusDetails($orderDetails)
    {
        return $this->_mpRmaHelper->getStatusDetails($orderDetails);
    }
}
