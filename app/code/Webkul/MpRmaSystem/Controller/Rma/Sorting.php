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

class Sorting extends \Magento\Framework\App\Action\Action
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
            $sortCol = trim($this->getRequest()->getPost("sort_col"));
            $sortOrder = trim($this->getRequest()->getPost("sort_order"));
            $sortOrder = strtoupper($sortOrder);
            
            if ($sortCol == "wk_order_ref") {
                $sortCol = "order_ref";
            } elseif ($sortCol == "wk_date") {
                $sortCol = "created_date";
            } elseif ($sortCol == "wk_customer") {
                $sortCol = "customer_name";
            } else {
                $sortCol = "id";
            }

            if ($this->getRequest()->getPost("type") == 2) {
                $helper->setFilter("seller_grid_sorting_order", $sortOrder);
                $helper->setFilter("seller_grid_sorting_field", $sortCol);
            } else {
                $helper->setFilter("buyer_grid_sorting_order", $sortOrder);
                $helper->setFilter("buyer_grid_sorting_field", $sortCol);
            }
        }
        $result = $this->_resultJsonFactory->create();
        $result->setData(["success" => 1]);
        return $result;
    }
}
