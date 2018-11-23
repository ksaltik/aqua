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
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollection;
use Webkul\MpRmaSystem\Helper\Data;

class Filter extends \Magento\Framework\App\Action\Action
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
        if ($this->getRequest()->getPost()) {
            $helper = $this->_mpRmaHelper;
            $rmaId = trim($this->getRequest()->getPost("rma_id"));
            $orderRef = trim($this->getRequest()->getPost("order_ref"));
            $status = trim($this->getRequest()->getPost("status"));
            $from = trim($this->getRequest()->getPost("from"));
            $customer = trim($this->getRequest()->getPost("customer"));
            if ($from != "") {
                $from = date("Y-m-d", strtotime($from));
            }

            $to = trim($this->getRequest()->getPost("to"));
            if ($to != "") {
                $to = date("Y-m-d", strtotime($to));
            }

            if ($this->getRequest()->getPost("type") == 2) {
                $helper->setFilter("seller_filter_rma_id", $rmaId);
                $helper->setFilter("seller_filter_order_ref", $orderRef);
                $helper->setFilter("seller_filter_status", $status);
                $helper->setFilter("seller_filter_date_from", $from);
                $helper->setFilter("seller_filter_date_to", $to);
                $helper->setFilter("seller_filter_customer", $customer);
            } else {
                $helper->setFilter("buyer_filter_rma_id", $rmaId);
                $helper->setFilter("buyer_filter_order_ref", $orderRef);
                $helper->setFilter("buyer_filter_status", $status);
                $helper->setFilter("buyer_filter_date_from", $from);
                $helper->setFilter("buyer_filter_date_to", $to);
            }
        }

        $result = $this->_resultJsonFactory->create();
        $result->setData(["success" => 1]);
        return $result;
    }
}
