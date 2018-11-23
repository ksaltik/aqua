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
namespace Webkul\MpRmaSystem\Helper;

use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Item\CollectionFactory as InvoiceItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Item\CollectionFactory as ShipmentItemCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Webkul\Marketplace\Model\ResourceModel\Seller\CollectionFactory as SellerCollection;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory as ProductCollection;
use Webkul\Marketplace\Model\ResourceModel\Orders\CollectionFactory as OrdersCollection;
use Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory as SalesListCollection;
use Webkul\MpRmaSystem\Model\ResourceModel\Details\CollectionFactory as DetailsCollection;
use Webkul\MpRmaSystem\Model\ResourceModel\Reasons\CollectionFactory as ReasonsCollection;
use Webkul\MpRmaSystem\Model\ResourceModel\Conversation\CollectionFactory as ConversationCollection;
use Magento\Sales\Block\Order\Item\Renderer\DefaultRendererFactory;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\CatalogInventory\Api\StockManagementInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ADMIN = 'Admin';
    const NEW_RMA = 'new_rma';
    const UPDATE_RMA = 'update_rma';
    const RMA_MESSAGE = 'rma_message';

    const RESOLUTION_REFUND = 1;
    const RESOLUTION_REPLACE = 2;
    const RESOLUTION_CANCEL = 3;

    const RESOLUTION_REFUND_LABEL = 'Refund';
    const RESOLUTION_REPLACE_LABEL = 'Replace';
    const RESOLUTION_CANCEL_LABEL = 'Cancel Items';

    const ORDER_NOT_DELIVERED = 0;
    const ORDER_DELIVERED = 1;
    const ORDER_NOT_APPLICABLE = 2;

    const ORDER_DELIVERED_LABEL = 'Delivered';
    const ORDER_NOT_DELIVERED_LABEL = 'Not Delivered';
    const ORDER_NOT_APPLICABLE_LABEL = 'Not Applicable';

    const SELLER_STATUS_PENDING = 0;
    const SELLER_STATUS_PACKAGE_NOT_RECEIVED = 1;
    const SELLER_STATUS_PACKAGE_RECEIVED = 2;
    const SELLER_STATUS_PACKAGE_DISPATCHED = 3;
    const SELLER_STATUS_SOLVED = 4;
    const SELLER_STATUS_DECLINED = 5;
    const SELLER_STATUS_ITEM_CANCELED = 6;

    const FINAL_STATUS_PENDING = 0;
    const FINAL_STATUS_CANCELED = 1;
    const FINAL_STATUS_DECLINED = 2;
    const FINAL_STATUS_SOLVED = 3;
    const FINAL_STATUS_CLOSED = 4;

    const RMA_STATUS_PENDING = 0;
    const RMA_STATUS_PROCESSING = 1;
    const RMA_STATUS_SOLVED = 2;
    const RMA_STATUS_DECLINED = 3;
    const RMA_STATUS_CANCELED = 4;

    const TYPE_BUYER = "buyer";
    const TYPE_SELLER = "seller";

    const FILTER_STATUS_ALL = 0;
    const FILTER_STATUS_PENDING = 1;
    const FILTER_STATUS_PROCESSING = 2;
    const FILTER_STATUS_SOLVED = 3;
    const FILTER_STATUS_DECLINED  = 4;
    const FILTER_STATUS_CANCELED = 5;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_session;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var SellerCollection
     */
    protected $_sellerCollection;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    /**
     * @var OrdersCollection
     */
    protected $_ordersCollection;

    /**
     * @var OrderCollection
     */
    protected $_orderCollectionFactory;

    /**
     * @var DetailsCollection
     */
    protected $_detailsCollection;

    /**
     * @var ReasonsCollection
     */
    protected $_reasonsCollection;

    /**
     * @var ConversationCollection
     */
    protected $_conversationCollection;

    /**
     * @var \Webkul\MpRmaSystem\Model\DetailsFactory
     */
    protected $_rma;

    /**
     * @var \Webkul\MpRmaSystem\Model\ReasonsFactory
     */
    protected $_reason;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var OrderFactory
     */
    protected $_order;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $_memoLoader;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender
     */
    protected $_memoSender;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_currency;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Sales\Api\CreditmemoManagementInterface
     */
    protected $_creditmemoManagement;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Session\SessionManager $session
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param SellerCollection $sellerCollection
     * @param ProductCollection $productCollection
     * @param OrdersCollection $ordersCollection
     * @param OrderCollection $orderCollectionFactory
     * @param DetailsCollection $detailsCollectionFactory
     * @param ReasonsCollection $reasonsCollectionFactory
     * @param ConversationCollection $conversationCollectionFactory
     * @param \Webkul\MpRmaSystem\Model\DetailsFactory $rma
     * @param \Webkul\MpRmaSystem\Model\ReasonsFactory $reason
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param OrderFactory $orderFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Locale\CurrencyInterface $currency
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManager $session,
        \Magento\Framework\Filesystem $fileSystem,
        SellerCollection $sellerCollection,
        ProductCollection $productCollection,
        InvoiceItemCollection $invoiceItemCollection,
        ShipmentItemCollection $shipmentItemCollection,
        OrdersCollection $ordersCollection,
        SalesListCollection $salesListCollection,
        OrderCollection $orderCollectionFactory,
        DetailsCollection $detailsCollectionFactory,
        ReasonsCollection $reasonsCollectionFactory,
        ConversationCollection $conversationCollectionFactory,
        \Webkul\MpRmaSystem\Model\DetailsFactory $rma,
        \Webkul\MpRmaSystem\Model\ReasonsFactory $reason,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        OrderFactory $orderFactory,
        \Webkul\MpRmaSystem\Model\ItemsFactory $items,
        \Magento\Framework\Registry $registry,
        \Webkul\MpRmaSystem\Model\ResourceModel\Details $detailsResource,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        \Magento\Sales\Model\Order\Email\Sender\CreditmemoSender $creditmemoSender,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Locale\CurrencyInterface $currency,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Api\CreditmemoManagementInterface $creditmemoManagement,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        DefaultRendererFactory $defaultRenderer,
        \Magento\Framework\Escaper $escaper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        StockManagementInterface $stockManagement,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $priceIndexer,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Module\ResourceInterface $moduleResource,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $context->getRequest();
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_session = $session;
        $this->_fileSystem = $fileSystem;
        $this->_sellerCollection = $sellerCollection;
        $this->_productCollection = $productCollection;
        $this->_invoiceItemCollection = $invoiceItemCollection;
        $this->_shipmentItemCollection = $shipmentItemCollection;
        $this->_ordersCollection = $ordersCollection;
        $this->_salesListCollection = $salesListCollection;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_detailsCollection = $detailsCollectionFactory;
        $this->_reasonsCollection = $reasonsCollectionFactory;
        $this->_conversationCollection = $conversationCollectionFactory;
        $this->_rma = $rma;
        $this->_reason = $reason;
        $this->_product = $productFactory;
        $this->_orderFactory = $orderFactory;
        $this->_items = $items;
        $this->_registry = $registry;
        $this->_detailsResource = $detailsResource;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_memoLoader = $creditmemoLoader;
        $this->_memoSender = $creditmemoSender;
        $this->_resource = $resource;
        $this->_currency = $currency;
        $this->_customerFactory = $customerFactory;
        $this->_creditmemoManagement = $creditmemoManagement;
        $this->_fileUploader = $fileUploaderFactory;
        $this->_defaultRenderer = $defaultRenderer;
        $this->_escaper = $escaper;
        $this->_stockRegistry = $stockRegistry;
        $this->_stockManagement = $stockManagement;
        $this->_priceIndexer = $priceIndexer;
        $this->_mpHelper = $mpHelper;
        $this->_priceCurrency = $priceCurrency;
        $this->_currencyFactory = $currencyFactory;
        $this->_moduleResource = $moduleResource;
        parent::__construct($context);
    }

    /**
     * Get Default Days to Request RMA
     *
     * @return int
     */
    public function getDefaultDays()
    {
        $path = "mprmasystem/settings/default_days";
        $scope = ScopeInterface::SCOPE_STORE;
        $days = (int) $this->_scopeConfig->getValue($path, $scope);
        if ($days <= 0) {
            $days = 30;
        }

        return $days;
    }

    /**
     * Check Whether Notification to Admin is Allowed or Not
     *
     * @return bool
     */
    public function isAllowedNotification()
    {
        $path = "mprmasystem/settings/admin_notification";
        $scope = ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($path, $scope);
    }

    /**
     * Get Admin Email Id
     *
     * @return string
     */
    public function getAdminEmail()
    {
        $path = "mprmasystem/settings/admin_email";
        $scope = ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($path, $scope);
    }

    /**
     * Get Current Customer Id
     *
     * @return int
     */
    public function getCustomerId()
    {
        $customerId = 0;
        if ($this->_customerSession->isLoggedIn()) {
            $customerId = (int) $this->_customerSession->getCustomerId();
        }

        return $customerId;
    }

    /**
     * Check Customer is Logged In or Not
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return true;
        }

        return false;
    }

    /**
     * Get Mediad Path
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
    }

    /**
     * Get Current RMA Id
     *
     * @return int
     */
    public function getCurrentRmaId()
    {
        $id = (int) $this->_request->getParam('id');
        return $id;
    }

    /**
     * Get Seller Details
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\Seller\Collection
     */
    public function getSellerDetails($sellerId)
    {
        $seller = false;
        $collection = $this->_sellerCollection
                            ->create()
                            ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
        foreach ($collection as $seller) {
            return $seller;
        }

        return $seller;
    }

    /**
     * Check Whether Customer Is Seller Or Not
     *
     * @param int $sellerId [optional]
     *
     * @return bool
     */
    public function isSeller($sellerId = '')
    {
        if ($sellerId == '') {
            $sellerId = $this->getSellerId();
        }

        $seller = $this->getSellerDetails($sellerId);
        if ($seller) {
            $isSeller = $seller->getIsSeller();
            if ($isSeller == 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Customer's Orders
     *
     * @param int $customerId [optional]
     *
     * @return array
     */
    public function getOrdersOfCustomer($customerId = '')
    {
        $days = $this->getDefaultDays();
        $from = date('Y-m-d', strtotime("-".$days." days"));
        $allowedStatus = ['pending', 'processing', 'complete'];
        if ($customerId == '') {
            $customerId = $this->getCustomerId();
        }

        $orders = $this->_orderCollectionFactory->create()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('status', ['in'=> $allowedStatus])
                        ->addFieldToFilter('created_at', ['from'  => $from])
                        ->setOrder('created_at', 'desc');

        return $orders;
    }

    /**
     * Get Guest's Orders
     *
     * @param int $customerId Optional
     *
     * @return array
     */
    public function getOrdersOfGuest($email = '')
    {
        if (strlen($email) <= 0) {
            $email = $this->getGuestEmailId();
        }

        $days = $this->getDefaultDays();
        $from = date('Y-m-d', strtotime("-".$days." days"));
        $allowedStatus = ['pending', 'processing', 'complete'];
        $orders = $this->_orderCollectionFactory->create()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('customer_email', $email)
                        ->addFieldToFilter('status', ['in'=> $allowedStatus])
                        ->addFieldToFilter('created_at', ['from'  => $from])
                        ->setOrder('created_at', 'desc');

        return $orders;
    }

    /**
     * Get Currency Symbol By Currency Code
     *
     * @param string $code Optional
     *
     * @return string
     */
    public function getCurrencySymbol($code = 'USD')
    {
        return $this->_currency->getCurrency($code)->getSymbol();
    }

    /**
     * Get Reasons
     *
     * @param int $type Optional
     *
     * @return array
     */
    public function getAllReasons()
    {
        $reasons = [];
        $collection = $this->_reasonsCollection
                            ->create()
                            ->addFieldToFilter('status', 1);
        foreach ($collection as $reason) {
            $reasons[$reason->getId()] = $this->_escaper->escapeHtml($reason->getReason());
        }

        return $reasons;
    }

    /**
     * Get Seller Id By Order Id
     *
     * @param int $orderId
     *
     * @return int
     */
    public function getSellerIdByOrderId($orderId)
    {
        $sellerId = 0;
        $collection = $this->_ordersCollection
                            ->create()
                            ->addFieldToFilter('order_id', $orderId);
        foreach ($collection as $order) {
            $sellerId = $order->getSellerId();
        }

        return $sellerId;
    }

    /**
     * Get Seller Id By Order Item Id
     *
     * @param int $itemId
     *
     * @return int
     */
    public function getSellerIdByOrderItemId($itemId)
    {
        $sellerId = null;
        $collection = $this->_salesListCollection
                            ->create()
                            ->addFieldToFilter('order_item_id ', $itemId);
        foreach ($collection as $item) {
            $sellerId = $item->getSellerId();
        }

        return $sellerId;
    }

    /**
     * Get Seller Id By Product Id
     *
     * @param int $productId
     *
     * @return int
     */
    public function getSellerIdByProductId($productId)
    {
        $sellerId = 0;
        $collection = $this->_productCollection
                            ->create()
                            ->addFieldToFilter('mageproduct_id', $productId);
        foreach ($collection as $order) {
            $sellerId = $order->getSellerId();
        }

        return $sellerId;
    }

    /**
     * Get All Rma of Customer
     *
     * @param int $customerId [optional]
     *
     * @return \Webkul\MpRmaSystem\Model\ResourceModel\Details\Collection
     */
    public function getAllRma($customerId = '')
    {
        if ($customerId == '') {
            $customerId = $this->getCustomerId();
        }

        $collection = $this->_detailsCollection
                            ->create()
                            ->addFieldToFilter('customer_id', $customerId);

        return $collection;
    }

    /**
     * Get All Rma of Seller
     *
     * @param int $sellerId
     *
     * @return \Webkul\MpRmaSystem\Model\ResourceModel\Details\Collection
     */
    public function getAllRmaForSeller($sellerId)
    {
        $collection = $this->_detailsCollection
                            ->create()
                            ->addFieldToFilter('seller_id', $sellerId);

        return $collection;
    }

    /**
     * Check For Valid RMA For Admin
     *
     * @return bool
     */
    public function isAdminRma()
    {
        $id = $this->getCurrentRmaId();
        $collection = $this->_detailsCollection
                            ->create()
                            ->addFieldToFilter('seller_id', 0)
                            ->addFieldToFilter('id', $id);
        if ($collection->getSize()) {
            return true;
        }

        return false;
    }

    /**
     * Check For Valid RMA To View
     *
     * @param int $type [optional]
     *
     * @return bool
     */
    public function isValidRma($type = 0)
    {
        $id = $this->getCurrentRmaId();
        $customerId = $this->getCustomerId();
        $sellerId = $this->getSellerId();
        $email = $this->getGuestEmailId();
        $collection = $this->_detailsCollection
                            ->create()
                            ->addFieldToFilter('id', $id);
        if ($type == 1) { // Checking for Customer's Requested RMA
            $collection->addFieldToFilter('customer_id', $customerId);
        } elseif ($type == 2) { // Checking for Guest's Requested RMA
            $collection->addFieldToFilter('customer_email', $email);
        } else { // Checking for Seller's RMA
            $collection->addFieldToFilter('seller_id', $sellerId);
        }

        if ($collection->getSize()) {
            return true;
        }

        return false;
    }

    /**
     * Get RMA Details by Id
     *
     * @param int $rmaId [optional]
     *
     * @return \Webkul\MpRmaSystem\Model\Details
     */
    public function getRmaDetails($rmaId = 0)
    {
        if ($rmaId == 0) {
            $rmaId = $this->getCurrentRmaId();
        }

        $rma = $this->_rma->create()->load($rmaId);
        return $rma;
    }

    /**
     * Get Reason by Id
     *
     * @param int $reasonId
     *
     * @return string
     */
    public function getReasonById($reasonId)
    {
        $reason = $this->_reason->create()->load($reasonId);
        if ($reason->getId()) {
            return $this->_escaper->escapeHtml($reason->getReason());
        }

        return "";
    }

    /**
     * Get Order Details by Id
     *
     * @param int $orderId
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder($orderId)
    {
        $order = $this->_orderFactory->create()->load($orderId);
        return $order;
    }

    /**
     * Get Order Item Details by Item Id
     *
     * @param int $orderId
     * @param int $itemId
     *
     * @return Magento\Sales\Model\Order\Item
     */
    public function getOrderItem($orderId, $itemId)
    {
        $order = $this->getOrder($orderId);
        $orderedItems = $order->getAllVisibleItems();
        foreach ($orderedItems as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }

        return "";
    }

    /**
     * Get Images by Rma Id
     *
     * @param int $rmaId
     *
     * @return array
     */
    public function getImages($rmaId)
    {
        $currentStore = $this->_storeManager->getStore();
        $type = \Magento\Framework\UrlInterface::URL_TYPE_MEDIA;
        $mediaUrl = $currentStore->getBaseUrl($type);
        $imageArray = [];
        $path = $this->getMediaPath()."marketplace/rma/".$rmaId."/*";
        $images = glob($path);
        foreach ($images as $image) {
            $fileName = explode("/", $image);
            $fileName = end($fileName);
            $imageUrl = $mediaUrl.'marketplace/rma/'.$rmaId."/".$fileName;
            $imageArray[] = $imageUrl;
        }

        return $imageArray;
    }

    /**
     * Get Conversation on Rma by RMA Id
     *
     * @param int $rmaId
     *
     * @return \Webkul\MpRmaSystem\Model\ResourceModel\Conversation\Collection
     */
    public function getConversations($rmaId)
    {
        $collection = $this->_conversationCollection
                            ->create()
                            ->addFieldToFilter("rma_id", $rmaId)
                            ->setOrder("created_time", "desc");
        return $collection;
    }

    /**
     * Get Customer/Seller Name By RMA Id
     *
     * @param int $rmaId
     *
     * @return string
     */
    public function getCustomerName($rmaId, $isSeller = true)
    {
        $rma = $this->_rma->create()->load($rmaId);
        if ($rma->getId()) {
            $customerId = $rma->getCustomerId();
            if ($isSeller) {
                $customerId = $rma->getSellerId();
            }

            $customer = $this->getCustomer($customerId);
            return $customer->getName();
        }

        return "";
    }

    /**
     * Get Customner by Customer Id
     *
     * @param int $customerId
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer($customerId)
    {
        $customer = $this->_customerFactory->create()->load($customerId);
        return $customer;
    }

    /**
     * Get Seller Status Title
     *
     * @param int $status
     *
     * @return string
     */
    public function getSellerStatusTitle($status)
    {
        if ($status == self::SELLER_STATUS_PENDING || $status == self::SELLER_STATUS_PACKAGE_NOT_RECEIVED) {
            $sellerStatus = "Pending";
        } elseif ($status == self::SELLER_STATUS_PACKAGE_RECEIVED) {
            $sellerStatus = "Processing";
        } elseif ($status == self::SELLER_STATUS_PACKAGE_DISPATCHED) {
            $sellerStatus = "Processing";
        } elseif ($status == self::SELLER_STATUS_SOLVED) {
            $sellerStatus = "Solved";
        } elseif ($status == self::SELLER_STATUS_DECLINED) {
            $sellerStatus = "Declined";
        } else {
            $sellerStatus = "Item Canceled";
        }

        return $sellerStatus;
    }

    /**
     * Get Resolution Type Title
     *
     * @param int $status
     *
     * @return string
     */
    public function getResolutionTypeTitle($status)
    {
        if ($status == self::RESOLUTION_REFUND) {
            $resolution = self::RESOLUTION_REFUND_LABEL;
        } elseif ($status == self::RESOLUTION_REPLACE) {
            $resolution = self::RESOLUTION_REPLACE_LABEL;
        } else {
            $resolution = self::RESOLUTION_CANCEL_LABEL;
        }

        $resolution = __($resolution);
        return $resolution;
    }

    /**
     * Get Order Status Title
     *
     * @param int $status
     *
     * @return string
     */
    public function getOrderStatusTitle($status)
    {
        if ($status == self::ORDER_DELIVERED) {
            $orderStatus = self::ORDER_DELIVERED_LABEL;
        } elseif ($status == self::ORDER_NOT_DELIVERED) {
            $orderStatus = self::ORDER_NOT_DELIVERED_LABEL;
        } else {
            $orderStatus = self::ORDER_NOT_APPLICABLE_LABEL;
        }

        $orderStatus = __($orderStatus);
        return $orderStatus;
    }

    /**
     * Get RMA Status Title
     *
     * @param int $status
     * @param int $finalStatus
     *
     * @return string
     */
    public function getRmaStatusTitle($status, $finalStatus = 0)
    {
        if ($finalStatus == self::FINAL_STATUS_PENDING) {
            if ($status == self::RMA_STATUS_PROCESSING) {
                $rmaStatus = "Processing";
            } elseif ($status == self::RMA_STATUS_SOLVED) {
                $rmaStatus = "Solved";
            } elseif ($status == self::RMA_STATUS_DECLINED) {
                $rmaStatus = "Declined";
            } elseif ($status == self::RMA_STATUS_CANCELED) {
                $rmaStatus = "Canceled";
            } else {
                $rmaStatus = "Pending";
            }
        } else {
            if ($finalStatus == self::FINAL_STATUS_CANCELED) {
                $rmaStatus = "Canceled";
            } elseif ($finalStatus == self::FINAL_STATUS_DECLINED) {
                $rmaStatus = "Declined";
            } elseif ($finalStatus == self::FINAL_STATUS_SOLVED || $finalStatus == self::FINAL_STATUS_CLOSED) {
                $rmaStatus = "Solved";
            } else {
                $rmaStatus = "Pending";
            }
        }

        return $rmaStatus;
    }

    /**
     * Get Seller's All Status
     *
     * @param int $status
     *
     * @return array
     */
    public function getAllStatus($resolutionType, $orderStatus = 1)
    {
        if ($resolutionType == self::RESOLUTION_CANCEL) {
            $allStatus = [
                            self::SELLER_STATUS_PENDING => 'Pending',
                            self::SELLER_STATUS_DECLINED => 'Declined',
                            self::SELLER_STATUS_ITEM_CANCELED => 'Item Canceled'
                        ];
        } else {
            if ($orderStatus == self::ORDER_DELIVERED) {
                $allStatus = [
                            self::SELLER_STATUS_PENDING => 'Pending',
                            self::SELLER_STATUS_PACKAGE_NOT_RECEIVED => 'Not Receive Package yet',
                            self::SELLER_STATUS_PACKAGE_RECEIVED => 'Received Package',
                            self::SELLER_STATUS_PACKAGE_DISPATCHED => 'Dispatched Package',
                            self::SELLER_STATUS_DECLINED => 'Declined'
                    ];
            } elseif ($orderStatus == self::ORDER_NOT_DELIVERED) {
                $allStatus = [
                            self::SELLER_STATUS_PENDING => 'Pending',
                            self::SELLER_STATUS_DECLINED => 'Declined'
                    ];
            } else {
                $allStatus = [
                            self::SELLER_STATUS_PENDING => 'Pending',
                            self::SELLER_STATUS_DECLINED => 'Declined'
                    ];
            }
        }

        return $allStatus;
    }

    /**
     * Create Creditmemo
     *
     * @param array $data
     *
     * @return array
     */
    public function createCreditMemo($data)
    {
        $error = 0;
        $result = ['msg' => '', 'error' => ''];
        $rmaId = $data['rma_id'];
        $negative = $data['negative'];
        $rma = $this->getRmaDetails($rmaId);
        $orderId = $rma->getOrderId();
        $productDetails = $this->getRmaProductDetails($rmaId);
        $items = [];
        foreach ($productDetails as $product) {
            $items[$product->getItemId()] = ['qty' => $product->getQty()];
        }

        $memo = [
                    'items' => $items,
                    'do_offline' => 1,
                    'comment_text' => "",
                    'shipping_amount' => 0,
                    'adjustment_positive' => 0,
                    'adjustment_negative' => $negative
                ];

        try {
            $this->_memoLoader->setOrderId($orderId);
            $this->_memoLoader->setCreditmemoId("");
            $this->_memoLoader->setCreditmemo($memo);
            $this->_memoLoader->setInvoiceId("");
            $memo = $this->_memoLoader->load();
            if ($memo) {
                if (!$memo->isValidGrandTotal()) {
                    $result['msg'] = __('Total must be positive.');
                    $result['error'] = 1;
                    return $result;
                }

                if (!empty($memo['comment_text'])) {
                    $memo->addComment(
                        $memo['comment_text'],
                        isset($memo['comment_customer_notify']),
                        isset($memo['is_visible_on_front'])
                    );

                    $memo->setCustomerNote($memo['comment_text']);
                    $memo->setCustomerNoteNotify(isset($memo['comment_customer_notify']));
                }

                if (isset($memo['do_offline'])) {
                    //do not allow online refund for Refund to Store Credit
                    if (!$memo['do_offline'] && !empty($memo['refund_customerbalance_return_enable'])) {
                        $result['msg'] = __('Cannot create online refund.');
                        $result['error'] = 1;
                        return $result;
                    }
                }

                $memoManagement = $this->_creditmemoManagement;
                $memoManagement->refund($memo, (bool)$memo['do_offline'], !empty($memo['send_email']));

                if (!empty($memo['send_email'])) {
                    $this->_memoSender->send($memo);
                }

                $result['msg'] = __('Credit memo generated successfully.');
                $result['error'] = 0;
                $result['memo_id'] = $memo->getId();
                return $result;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $result['msg'] = $e->getMessage();
            $result['error'] = 1;
        } catch (\Exception $e) {
            $result['msg'] = __('Unable to save credit memo right now.');
            $result['error'] = 1;
        }

        return $result;
    }
    
    /**
     * Get Close RMA Text
     *
     * @param int $status
     *
     * @return string
     */
    public function getCloseRmaLabel($status)
    {
        $label = "";
        if ($status == 1) {
            $label = __("RMA is canceled by Customer.");
        } elseif ($status == 2) {
            $label = __("RMA is declined by Seller.");
        } elseif ($status == 3 || $status == 4) {
            $label = __("RMA is Solved.");
        }

        return $label;
    }
    public function cancelOrder($rmaId)
    {
        $orderId = $this->getRmaProductDetails($rmaId)->getData()[0]['order_id'];
        $order = $this->getOrder($orderId);
        $cancel = true;
        foreach ($order->getAllItems() as $item) {
            if(intval($item->getQtyCanceled())!=intval($item->getQtyOrdered())) {
                $cancel = false;
                break;
            }
        }
        if($cancel) {
            $status = "canceled";
            $order->setState($status)->setStatus($status)->save();
        }
    }
    /**
     * Get Product Details of Requested RMA
     *
     * @param int $rmaId
     *
     * @return collection object
     */
    public function getRmaProductDetails($rmaId)
    {
        return $this->_detailsResource->getRmaProductDetails($rmaId);
    }

    /**
     * Get Requested Quantity of RMA by Order Item Id
     *
     * @param int $itemId
     * @param int $orderId
     *
     * @return int
     */
    public function getItemRmaQty($itemId, $orderId)
    {
        $totalQty = 0;
        $collection = $this->_detailsCollection->create();
        $tableName = $this->_resource->getTableName('marketplace_rma_items');
        $sql = "main_table.id = rma_items.rma_id ";
        $collection->getSelect()->join(['rma_items' => $tableName], $sql, ['*']);
        $collection->addFilterToMap('item_id', 'rma_items.item_id');
        $condition = "(";
        $condition .= "(rma_items.item_id = $itemId)";
        $condition .= " AND (order_id = $orderId)";
        $condition .= " AND (final_status = 0)";
        $condition .= ")";
        $condition .= " OR ";
        $condition .= " (";
        $condition .= " (rma_items.is_qty_returned = 1)";
        $condition .= " AND (rma_items.item_id = $itemId)";
        $condition .= ")";
        $collection->getSelect()->where($condition);
        $collection->getSelect()->group("main_table.id");
        foreach ($collection as $item) {
            $totalQty += $item->getQty();
        }

        return $totalQty;
    }

    /**
     * Get Final Quantity Which Can Be Requested
     *
     * @param int $itemId
     * @param int $orderId
     * @param int $qty
     * @param int $type
     *
     * @return int
     */
    public function getRmaQty($itemId, $orderId, $qty, $type)
    {
        if ($type == 1) {
            $qty = 0;
            $collection = $this->_invoiceItemCollection
                                ->create()
                                ->addFieldToFilter('order_item_id', $itemId);
            foreach ($collection as $item) {
                $qty += $item->getQty();
            }
        }

        $rmaQty = $this->getItemRmaQty($itemId, $orderId);
        $qty = $qty - $rmaQty;
        return $qty;
    }

    /**
     * Check Whether RMA is Allowed for Quantity or Not
     *
     * @param int $itemId
     * @param int $orderId
     * @param int $qty
     *
     * @return bool
     */
    public function isRmaAllowed($itemId, $orderId, $qty)
    {
        $rmaQty = $this->getItemRmaQty($itemId, $orderId);
        $orderItem = $this->getOrderItem($orderId, $itemId);
        $totalQty = $orderItem->getQtyOrdered();
        $allowedQty = $totalQty - $rmaQty;
        if ($qty > $allowedQty) {
            return false;
        }

        return true;
    }

    /**
     * Send New RMA Email
     *
     * @param array $details
     */
    public function sendNewRmaEmail($details = [])
    {
        $details['template'] = self::NEW_RMA;
        $orderStatus = $this->getOrderStatusTitle($details['rma']['order_status']);
        $resolutionType = $this->getResolutionTypeTitle($details['rma']['resolution_type']);
        $additionalInfo = $details['rma']['additional_info'];
        $customerName = $details['name'];
        $templateVars = [
                            'name' => $customerName,
                            'rma_id' => $details['rma']['rma_id'],
                            'order_id' => $details['rma']['order_ref'],
                            'order_status' => $orderStatus,
                            'resolution_type' => $resolutionType,
                            'additional_info' => $additionalInfo
                        ];

        $details['email'] = $details['rma']['customer_email'];
        if ($details['rma']['customer_id'] > 0) {
            $msg = __("New RMA is requested by customer.");
        } else {
            $msg = __("New RMA is requested by guest.");
        }

        //send to seller
        $sellerId = $details['rma']['seller_id'];
        if ($sellerId > 0) {
            $seller = $this->getCustomer($sellerId);
            $email = $seller->getEmail();
            $sellerName = $seller->getName();
            $templateVars['msg'] = $msg;
            $templateVars['name'] = $sellerName;
            $details['email'] = $email;
            $details['template_vars'] = $templateVars;
            $this->sendEmail($details);
        }
        
        //send to admin
        if ($this->isAllowedNotification()) {
            $adminEmail = $this->getAdminEmail();
            $templateVars['msg'] = $msg;
            $templateVars['name'] = self::ADMIN;
            $details['template_vars'] = $templateVars;
            $details['email'] = $adminEmail;
            $this->sendEmail($details);
        }

        //send to customer/guest
        $msg = __("You requested new RMA.");
        $templateVars['msg'] = $msg;
        $templateVars['name'] = $customerName;

        $details['template_vars'] = $templateVars;
        $details['email'] = $details['rma']['customer_email'];
        $this->sendEmail($details);
    }

    /**
     * Send Update RMA Email
     *
     * @param array $details
     */
    public function sendUpdateRmaEmail($details = [])
    {
        $details['template'] = self::UPDATE_RMA;
        $rma = $this->getRmaDetails($details['rma_id']);
        $finalStatus = $rma->getFinalStatus();
        $sellerStatus = $rma->getSellerStatus();
        $status = $rma->getStatus();
        $rmaStatusTitle = $this->getRmaStatusTitle($status, $finalStatus);
        $sellerStatusTitle = $this->getSellerStatusTitle($sellerStatus);
        $sellerId = $rma->getSellerId();
        $customerId = $rma->getCustomerId();
        $email = $rma->getCustomerEmail();
        $customerEmail = $rma->getCustomerEmail();
        $adminEmail = $this->getAdminEmail();
        $adminName = self::ADMIN;
        if ($customerId > 0) {
            $customer = $this->getCustomer($customerId);
            $customerName = $customer->getName();
        } else {
            $customerName = 'Guest';
        }

        $templateVars = [
                            'name' => $customerName,
                            'rma_id' => $details['rma_id'],
                            'rma_status' => $rmaStatusTitle,
                            'seller_status' => $sellerStatusTitle
                        ];
        //send to customer/guest
        $msg = __("RMA status is updated.");
        $templateVars['msg'] = $msg;
        $details['template_vars'] = $templateVars;
        $details['email'] = $customerEmail;
        $this->sendEmail($details);
        //send to seller
        $msg = __("RMA status is updated.");
        $templateVars['msg'] = $msg;
        $seller = $this->getCustomer($sellerId);
        $email = $seller->getEmail();
        $sellerName = $seller->getName();
        $templateVars['name'] = $sellerName;
        $details['email'] = $email;
        $details['template_vars'] = $templateVars;
        $this->sendEmail($details);
        //send to admin
        $templateVars['name'] = self::ADMIN;
        $details['template_vars'] = $templateVars;
        $this->sendNotificationToAdmin($details);
    }

    /**
     * Send New Message Email
     *
     * @param array $details
     */
    public function sendNewMessageEmail($details)
    {
        $isAdmin = false;
        $details['template'] = self::RMA_MESSAGE;
        $rmaId = $details['rma_id'];
        $message = $details['message'];
        $senderType = $details['sender_type'];
        $rma = $this->getRmaDetails($rmaId);
        $sellerId = $rma->getSellerId();
        $seller = $this->getCustomer($rma->getSellerId());
        $customerId = $rma->getCustomerId();
        $customerEmail = $rma->getCustomerEmail();
        $adminEmail = $this->getAdminEmail();
        $adminName = self::ADMIN;
        $customerName = $this->getSenderName($customerId);
        $senderData = $this->getSenderDetails($sellerId);

        if ($senderType == 1) { // seller send message
            $msg = __("Seller sent message on RMA #%1", $rmaId);
            $senderData = [
                            'email' => $customerEmail,
                            'name' => $customerName
                        ];
        } elseif ($senderType == 2) { // Customer send message
            $msg = __("Customer sent message on RMA #%1", $rmaId);
        } elseif ($senderType == 3) { // Guest send message
            $msg = __("Guest sent message on RMA #%1", $rmaId);
        } else { // Admin send message
            $isAdmin = true;
            $msg = __("Admin sent message on RMA #%1", $rmaId);
        }

        $details['email'] = $senderData['email'];
        $templateVars = [
                        'name' => $senderData['name'],
                        'message' => $message,
                        'msg' => $msg
                    ];
        $details['template_vars'] = $templateVars;
        $this->sendEmail($details);
        if ($isAdmin) {
            //Send to Customer
            $details['email'] = $customerEmail;
            $details['template_vars']['name'] = $customerName;
        } else {
            //Send to Admin
            $this->sendNotificationToAdmin($details);
        }
    }

    /**
     * Send Email
     *
     * @param array $details
     */
    public function sendEmail($details)
    {
        try {
            $adminEmail = $this->getAdminEmail();
            $area = \Magento\Framework\App\Area::AREA_FRONTEND;
            $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            $sender = ['name' => self::ADMIN, 'email' => $adminEmail];
            $transport = $this->_transportBuilder
                            ->setTemplateIdentifier($details['template'])
                            ->setTemplateOptions(['area' => $area, 'store' => $storeId])
                            ->setTemplateVars($details['template_vars'])
                            ->setFrom($sender)
                            ->addTo($details['email'])
                            ->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            return;
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
        }
    }

    /**
     * Check Guest Details
     *
     * @param int $incrementId
     * @param string $emil
     *
     * @return bool
     */
    public function authenticate($incrementId, $email)
    {
        $orders = $this->_orderCollectionFactory->create()
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('customer_email', $email)
                        ->addFieldToFilter('increment_id', $incrementId)
                        ->addFieldToFilter('customer_is_guest', 1)
                        ->setPageSize(1);
        if ($orders->getSize()) {
            return true;
        }

        return false;
    }

    /**
     * Login Guest
     *
     * @param string $email
     */
    public function loginGuest($email)
    {
        $this->_session->setGuestEmailId($email);
    }

    /**
     * Check Whether Guest is Logged In or Not
     *
     * @return bool
     */
    public function isGuestLoggedIn()
    {
        $email = trim($this->_session->getGuestEmailId());
        if ($email != "") {
            return true;
        }

        return false;
    }

    /**
     * Get Guest Email Id
     *
     * @return bool
     */
    public function getGuestEmailId()
    {
        return trim($this->_session->getGuestEmailId());
    }

    /**
     * Get List of Jquery Errors
     *
     * @return array
     */
    public function getJsErrorList()
    {
        $errors = [];
        $errors[] = "There is some error in preview image.";
        $errors[] = "Quantity not allowed for RMA.";
        $errors[] = "Please select item.";
        $errors[] = "Please select quantity.";
        $errors[] = "Image type not allowed.";
        return $errors;
    }

    /**
     * Validate Image
     *
     * @param string $imagePath
     *
     * @return bool
     */
    public function validateImage($imagePath)
    {
        try {
            $imageDetails = getimagesize($imagePath);
            $success = true;
        } catch (\Exception $e) {
            $success = false;
        }

        return $success;
    }

    /**
     * Check Whether Image Upload Allowed or Not
     *
     * @param int $numberOfImages
     *
     * @return bool
     */
    public function isAllowedImageUpload($numberOfImages)
    {
        $success = true;
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
        if ($numberOfImages > 0) {
            for ($i = 0; $i < $numberOfImages; $i++) {
                $fileId = "showcase[$i]";
                try {
                    $uploader = $this->_fileUploader->create(['fileId' => $fileId]);
                    $uploader->setAllowedExtensions($allowedExtensions);
                    $imageData = $uploader->validateFile();
                    $isValidImage = $this->validateImage($imageData['tmp_name']);
                    if (!$isValidImage) {
                        $success =  false;
                        break;
                    }
                } catch (\Exception $e) {
                    $success =  false;
                    break;
                }
            }
        }

        return $success;
    }

    /**
     * Get Skipped Images Indexes
     *
     * @return array
     */
    public function getSkippedImagesIndex()
    {
        $skippedIndexs = $this->_request->getParam('skip_checked');
        $skippedIndexs = trim($skippedIndexs);
        if (strpos($skippedIndexs, ",") !== false) {
            $skippedIndexs = explode(",", $skippedIndexs);
        } else {
            if ($skippedIndexs == "") {
                $skippedIndexs = [];
            } else {
                $skippedIndexs = [$skippedIndexs];
            }
        }

        return $skippedIndexs;
    }

    /**
     * Upload All Images of Rma
     *
     * @param int $numberOfImages
     * @param int $id
     */
    public function uploadImages($numberOfImages, $id)
    {
        $skippedIndexs = $this->getSkippedImagesIndex();
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
        if ($numberOfImages > 0) {
            $uploadPath = $this->_fileSystem
                                ->getDirectoryRead(DirectoryList::MEDIA)
                                ->getAbsolutePath('marketplace/rma/');
            $uploadPath .= $id;
            $count = 0;
            for ($i = 0; $i < $numberOfImages; $i++) {
                $fileId = "showcase[$i]";
                ++$count;
                $this->uploadImage($fileId, $uploadPath, $count);
            }
        }
    }

    /**
     * Upload Image of Rma
     *
     * @param string $fileId
     * @param string $uploadPath
     * @param int $count
     */
    public function uploadImage($fileId, $uploadPath, $count)
    {
        $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
        try {
            $uploader = $this->_fileUploader->create(['fileId' => $fileId]);
            $uploader->setAllowedExtensions($allowedExtensions);
            $imageData = $uploader->validateFile();
            $name = $imageData['name'];
            $ext = explode('.', $name);
            $ext = strtolower(end($ext));
            $imageName = 'image'.$count.'.'.$ext;
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->save($uploadPath, $imageName);
        } catch (\Exception $e) {
            $error =  true;
        }
    }

    /**
     * Upload Image of Rma
     *
     * @param int $customerId
     *
     * @return string
     */
    public function getSenderName($customerId)
    {
        if ($customerId > 0) {
            $customer = $this->getCustomer($customerId);
            $customerName = $customer->getName();
        } else {
            $customerName = __('Guest');
        }

        return $customerName;
    }

    /**
     * Send Notification to Admin
     *
     * @param array $details
     */
    public function sendNotificationToAdmin($details)
    {
        $adminEmail = $this->getAdminEmail();
        $adminName = self::ADMIN;
        if ($this->isAllowedNotification()) {
            $details['email'] = $adminEmail;
            $details['template_vars']['name'] = $adminName;
            $this->sendEmail($details);
        }
    }

    /**
     * Get Sender Details
     *
     * @param int $sellerId
     */
    public function getSenderDetails($sellerId)
    {
        $result = [];
        if ($sellerId > 0) {
            $seller = $this->getCustomer($sellerId);
            $result['email'] = $seller->getEmail();
            $result['name'] = $seller->getName();
        } else {
            $result['email'] = $this->getAdminEmail();
            $result['name'] = self::ADMIN;
        }

        return $result;
    }

    /**
     * Get Rma Reason Lable
     *
     * @return string
     */
    public function getRmaResonsLabel()
    {
        $reasonLables = [];
        $rmaId = $this->getCurrentRmaId();
        $collection = $this->_items
                            ->create()
                            ->getCollection()
                            ->addFieldToFilter("rma_id", $rmaId);
        foreach ($collection as $item) {
            $reasonLables[] = $this->getReasonById($item->getReasonId());
        }

        return implode(", ", $reasonLables);
    }

    /**
     * Set Rma Items
     *
     * @param array $productIds
     * @param array $allReasons
     * @param array $allQtys
     * @param array $allPrices
     * @param int $rmaId
     *
     * @return array
     */
    public function setItemsData($productIds, $allReasons, $allQtys, $allPrices, $rmaId)
    {
        try {
            foreach ($productIds as $itemId => $productId) {
                $data = [];
                $data['rma_id'] = $rmaId;
                $data['item_id'] = $itemId;
                $data['product_id'] = $productId;
                $data['qty'] = $allQtys[$itemId];
                $data['price'] = $allPrices[$itemId];
                $data['reason_id'] = $allReasons[$itemId];
                $this->_items->create()->setData($data)->save();
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get Seller Details By Product Id
     *
     * @param int $productId
     *
     * @return array
     */
    public function getSellerDetailsByProductId($productId)
    {
        $details = ["seller_id" => 0, "seller_name" => "Admin"];
        $collection = $this->_productCollection
                            ->create()
                            ->addFieldToFilter('mageproduct_id', $productId);
        foreach ($collection as $order) {
            $sellerId = $order->getSellerId();
            $seller = $this->getSellerDetails($sellerId);
            if ($seller) {
                $details = ["seller_id" => $sellerId, "seller_name" => $seller->getShopUrl()];
            }
        }

        return $details;
    }

    /**
     * Get Params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_request->getParams();
    }

    /**
     * Get All Rma Items For Email Template
     *
     * @return collection
     */
    public function getAllItems()
    {
        $rmaId = $this->getRegistry("rma_id");
        return $this->getRmaProductDetails($rmaId);
    }

    /**
     * Set Data In Registry
     *
     * @param string $key
     * @param mix $value
     *
     * @return array
     */
    public function setRegistry($key, $value)
    {
        $this->_registry->register($key, $value);
    }

    /**
     * Get Data In Registry
     *
     * @param string $key
     *
     * @return array
     */
    public function getRegistry($key)
    {
        return $this->_registry->registry($key);
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
        $html = "";
        $block = $this->_defaultRenderer->create();
        $block->setItem($orderItem);
        if ($_options = $block->getItemOptions()) {
            $html .= "<dl class='item-options'>";
            foreach ($_options as $_option) {
                $html .= "<dt>";
                $html .= $block->escapeHtml($_option['label']);
                $html .= "</dt>";
                if (!$block->getPrintStatus()) {
                    $_formatedOptionValue = $block->getFormatedOptionValue($_option);
                    $html .= "<dd>";
                    if (isset($_formatedOptionValue['full_view'])) {
                        $html .= $_formatedOptionValue['full_view'];
                    } else {
                        $html .= $_formatedOptionValue['value'];
                    }

                    $html .= "</dd>";
                } else {
                    if (isset($_option['print_value'])) {
                        $label = $_option['print_value'];
                    } else {
                        $label = $_option['value'];
                    }

                    $html .= "<dd>";
                    $html .= nl2br($block->escapeHtml($label));
                    $html .= "</dd>";
                }
            }

            $html .= "</dl>";
        }

        $addtInfoBlock = $block->getProductAdditionalInformationBlock();
        if ($addtInfoBlock) {
            $html .= $addtInfoBlock->setItem($orderItem)->toHtml();
        }

        return $html;
    }

    /**
     * Get Valid Price Of Item For RMA Refund
     *
     * @param Object $item
     *
     * @return float
     */
    public function getItemFinalPrice($item)
    {
        $rmaQty = $item->getQty();
        $qty = $item->getQtyOrdered();
        $totalPrice = $item->getRowTotal();
        $discountAmount = $item->getDiscountAmount();
        $taxAmount = $item->getTaxAmount();
        $finalPrice = $totalPrice + $taxAmount - $discountAmount;
        $unitPrice = $finalPrice / $qty;
        $finalPrice = $unitPrice * $rmaQty;
        return $finalPrice;
    }

    public function getStatusDetails($orderDetails)
    {
        $result = [];
        foreach ($orderDetails as $sellerId => $items) {
            $collection = $this->_invoiceItemCollection
                                ->create()
                                ->addFieldToFilter("order_item_id", ["in" => $items]);
            if ($collection->getSize()) {
                $result[$sellerId]['order_status'] = self::ORDER_DELIVERED;
            } else {
                $result[$sellerId]['order_status'] = self::ORDER_NOT_DELIVERED;
            }

            $collection = $this->_shipmentItemCollection
                                ->create()
                                ->addFieldToFilter("order_item_id", ["in" => $items]);
            if ($collection->getSize()) {
                $result[$sellerId]['shipment_status'] = 1;
            } else {
                $result[$sellerId]['shipment_status'] = 0;
            }
        }

        return $result;
    }

    /**
     * Cancel Order Item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param int $qty
     */
    public function cancelOrderItem($item, $qty)
    {
        if ($item->getStatusId() !== OrderItem::STATUS_CANCELED) {
            $totalCanceledQty = $qty + $item->getQtyCanceled();
            $item->setQtyCanceled($totalCanceledQty);
            $item->setTaxCanceled(
                $item->getTaxCanceled() + $item->getBaseTaxAmount() * $item->getQtyCanceled() / $item->getQtyOrdered()
            );
            $item->setDiscountTaxCompensationCanceled(
                $item->getDiscountTaxCompensationCanceled() +
                $item->getDiscountTaxCompensationAmount() * $item->getQtyCanceled() / $item->getQtyOrdered()
            );
            $item->save();
            $this->returnItemStock($item, $qty);
            $this->_priceIndexer->reindexRow($item->getProductId());
        }
    }

    /**
     * Return Product Quantity To Stock
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param int $qty
     */
    public function returnItemStock($item, $qty)
    {
        $children = $item->getChildrenItems();
        $productId = $item->getProductId();
        $websiteId = $item->getStore()->getWebsiteId();
        if ($item->getId() && $productId && empty($children) && $qty) {
            $this->_stockManagement->backItemQty($productId, $qty, $websiteId);
        }
    }

    /**
     * Manage Stock By RMA Id
     *
     * @param int $rmaId
     *
     */
    public function manageStock($rmaId, $productDetails = false)
    {
        if (!$productDetails) {
            $productDetails = $this->getRmaProductDetails($rmaId);
        }

        foreach ($productDetails as $item) {
            $qty = $item->getQty();
            $this->returnItemStock($item, $qty);
        }
    }

    /**
     * Process RMA Cancellation
     *
     * @param int $rmaId
     */
    public function processCancellation($rmaId)
    {
        $productDetails = $this->getRmaProductDetails($rmaId);
        foreach ($productDetails as $item) {
            $qty = $item->getQty();
            $this->cancelOrderItem($item, $qty);
        }
    }

    /**
     * Update RMA Item Qunatity Status
     *
     * @param int $rmaId
     */
    public function updateRmaItemQtyStatus($rmaId)
    {
        $collection = $this->_items
                        ->create()
                        ->getCollection()
                        ->addFieldToFilter("rma_id", $rmaId);
        foreach ($collection as $item) {
            $rmaData['is_qty_returned'] = 1;
            $item->addData(['is_qty_returned' => 1])
                ->setId($item->getId())
                ->save();
        }
    }

    /**
     * Get Current Seller Id
     *
     * @return int
     */
    public function getSellerId()
    {
        $version = (float) $this->_moduleResource->getDbVersion('Webkul_Marketplace');
        if ($version > 2.0) {
            return $this->_mpHelper->getCustomerId();
        } else {
            return $this->getCustomerId();
        }
    }

    public function updateMpOrder($orderId, $memoId)
    {
        $sellerId = $this->getSellerId();
        $collection = $this->_ordersCollection
                            ->create()
                            ->addFieldToFilter('seller_id', $sellerId)
                            ->addFieldToFilter('order_id', $orderId);
        $collection->getSelect()->limit(1);
        if ($collection->getSize()) {
            $creditmemoIds = [];
            foreach ($collection as $mpOrder) {
                $memoIds = $mpOrder->getCreditmemoId();
                if ($memoIds != "") {
                    if (strpos($memoIds, ",") !== false) {
                        $creditmemoIds = explode(',', $memoIds);
                    } else {
                        $creditmemoIds = [$memoIds];
                    }
                }

                array_push($creditmemoIds, $memoId);
                $mpOrder->setCreditmemoId(implode(',', $creditmemoIds));
                $mpOrder->save();
            }
        }
    }

    /**
     * Logout Guest
     *
     * @param string $email
     */
    public function logoutGuest()
    {
        $this->_session->unsGuestEmailId();
    }

    /**
     * Set Filters
     */
    public function setFilter($key, $value)
    {
        $this->_session->setData($key, $value);
    }

    public function getFilter($key)
    {
        $this->_session->getData($key);
    }

    public function getBuyerFilterRmaId()
    {
        return $this->_session->getData("buyer_filter_rma_id");
    }

    public function getBuyerFilterStatus()
    {
        return (int) $this->_session->getData("buyer_filter_status");
    }

    public function getBuyerFilterOrderRef()
    {
        return $this->_session->getData("buyer_filter_order_ref");
    }

    public function getBuyerFilterFromDate()
    {
        return $this->_session->getData("buyer_filter_date_from");
    }

    public function getBuyerFilterToDate()
    {
        return $this->_session->getData("buyer_filter_date_to");
    }

    public function getSellerFilterRmaId()
    {
        return $this->_session->getData("seller_filter_rma_id");
    }

    public function getSellerFilterStatus()
    {
        return (int) $this->_session->getData("seller_filter_status");
    }

    public function getSellerFilterOrderRef()
    {
        return $this->_session->getData("seller_filter_order_ref");
    }

    public function getSellerFilterFromDate()
    {
        return $this->_session->getData("seller_filter_date_from");
    }

    public function getSellerFilterToDate()
    {
        return $this->_session->getData("seller_filter_date_to");
    }

    public function getSellerFilterCustomer()
    {
        return $this->_session->getData("seller_filter_customer");
    }

    public function getAllRmaStatus()
    {
        $allStatus = [
                        self::FILTER_STATUS_ALL => "All Status",
                        self::FILTER_STATUS_PENDING => "Pending",
                        self::FILTER_STATUS_PROCESSING => "Processing",
                        self::FILTER_STATUS_SOLVED => "Solved",
                        self::FILTER_STATUS_DECLINED => "Declined",
                        self::FILTER_STATUS_CANCELED => "Canceled"
                    ];

        return $allStatus;
    }

    public function getMessage($type = self::TYPE_BUYER)
    {
        if ($type == self::TYPE_SELLER) {
            $allKeys = [
                    "seller_filter_rma_id",
                    "seller_filter_order_ref",
                    "seller_filter_status",
                    "seller_filter_date_from",
                    "seller_filter_date_to",
                    "seller_filter_customer"
                ];
            $msg = "You have no requested RMA.";
        } else {
            $allKeys = [
                    "buyer_filter_rma_id",
                    "buyer_filter_order_ref",
                    "buyer_filter_status",
                    "buyer_filter_date_from",
                    "buyer_filter_date_to"
                ];
            $msg = "You didn't request any RMA.";
        }

        foreach ($allKeys as $key) {
            if ($this->_session->getData($key)) {
                return __("No RMA found.");
            }
        }

        return __($msg);
    }

    public function applyFilter($collection, $type = self::TYPE_BUYER)
    {
        if ($type == self::TYPE_BUYER) {
            $rmaId = $this->getBuyerFilterRmaId();
            $status = $this->getBuyerFilterStatus();
            $orderRef = $this->getBuyerFilterOrderRef();
            $fromDate = $this->getBuyerFilterFromDate();
            $toDate = $this->getBuyerFilterToDate();
        } else {
            $rmaId = $this->getSellerFilterRmaId();
            $status = $this->getSellerFilterStatus();
            $orderRef = $this->getSellerFilterOrderRef();
            $fromDate = $this->getSellerFilterFromDate();
            $toDate = $this->getSellerFilterToDate();
        }

        if ($rmaId != "") {
            $collection->addFieldToFilter('id', $rmaId);
        }

        if ($orderRef != "") {
            $collection->addFieldToFilter('order_ref', ["like" => "%$orderRef%"]);
        }

        if ($fromDate != "" || $toDate != "") {
            if ($fromDate == "" && $toDate !== "") {
                $sql = "(date(created_date) <= '$toDate')";
                $collection->getSelect()->where($sql);
            } elseif ($fromDate != "" && $toDate == "") {
                $sql = "(date(created_date) >= '$fromDate')";
                $collection->getSelect()->where($sql);
            } else {
                $sql = "(date(created_date) >= '$fromDate' and date(created_date) <= '$toDate')";
                $collection->getSelect()->where($sql);
            }
        }

        $collection = $this->applyStatusFilter($collection, $status);

        if ($type == self::TYPE_SELLER) {
            $customer = $this->getSellerFilterCustomer();
            if ($customer != "") {
                $collection->addFieldToFilter('customer_name', ["like" => "%$customer%"]);
            }
        }

        return $collection;
    }

    public function applyStatusFilter($collection, $status)
    {
        if ($status != self::FILTER_STATUS_ALL) {
            $sql = "final_status > 0";
            if ($status == self::FILTER_STATUS_PENDING) {
                $sql = "(final_status = 0 and seller_status = 0)";
            } elseif ($status == self::FILTER_STATUS_PROCESSING) {
                $sql = "(final_status = 0 and seller_status = 1)";
            } elseif ($status == self::FILTER_STATUS_SOLVED) {
                $sql = "(final_status = 3) or (final_status = 4) or ((final_status = 0 and seller_status = 2))";
            } elseif ($status == self::FILTER_STATUS_DECLINED) {
                $sql = "(final_status = 2) or (seller_status = 3 and final_status = 0)";
            } elseif ($status == self::FILTER_STATUS_CANCELED) {
                $sql = "(final_status = 1) or (seller_status = 4 and final_status = 0)";
            }

            $collection->getSelect()->where($sql);
        }

        return $collection;
    }

    public function getSortingOrder($type = self::TYPE_BUYER)
    {
        if ($type == self::TYPE_BUYER) {
            $sortingOrder = $this->_session->getData("buyer_grid_sorting_order");
        } else {
            $sortingOrder = $this->_session->getData("seller_grid_sorting_order");
        }

        if ($sortingOrder == "") {
            $sortingOrder = "DESC";
        }

        return $sortingOrder;
    }

    public function getSortingField($type = self::TYPE_BUYER)
    {
        if ($type == self::TYPE_BUYER) {
            $field = $this->_session->getData("buyer_grid_sorting_field");
        } else {
            $field = $this->_session->getData("seller_grid_sorting_field");
        }

        if ($field == "") {
            $field = "id";
        }

        return $field;
    }

    public function getSortingFieldClass($type = self::TYPE_BUYER)
    {
        $field = $this->getSortingField($type);
        if ($field == "order_ref") {
            $class = "wk-filtered-order-ref";
        } elseif ($field == "created_date") {
            $class = "wk-filtered-date";
        } elseif ($field == "customer_name") {
            $class = "wk-filtered-rma-customer";
        } else {
            $class = "wk-filtered-rma-id";
        }

        return $class;
    }

    public function getSortingOrderClass($type = self::TYPE_BUYER)
    {
        $sortingOrder = $this->getSortingOrder($type);
        if ($sortingOrder == "ASC") {
            $class = "wk-asc-order";
        } else {
            $class = "wk-desc-order";
        }

        return $class;
    }

    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    public function getConvertedPrice($order, $price)
    {
        $currentCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $orderCurrencyCode = $order->getOrderCurrencyCode();

        if ($currentCurrencyCode == $orderCurrencyCode) {
            return $price;
        }

        $price = $this->convertPriceToBase($price, $orderCurrencyCode);
        $price = $this->convertPriceFromBase($price, $currentCurrencyCode);
        return $price;
    }

    public function convertPriceFromBase($amount, $currency)
    {
        $store = $this->_storeManager->getStore();
        return $store->getBaseCurrency()->convert($amount, $currency);
    }

    public function convertPriceToBase($amount, $currency)
    {
        $rate = $this->getCurrencyRateToBase($currency);
        $amount = $amount * $rate;
        return $amount;
    }

    public function getCurrencyRateToBase($currency)
    {
        $store = $this->_storeManager->getStore();
        $baseCurrencyCode = $store->getBaseCurrency()->getCode();
        $rate = $this->_currencyFactory->create()
                    ->load($currency)
                    ->getAnyRate($baseCurrencyCode);
        return $rate;
    }

    /**
     * Get Price With Currency
     *
     * @param float $price
     *
     * @return string
     */
    public function getPriceWithCurrency($price)
    {
        $price = $this->_priceCurrency->convertAndFormat($price);
        return $price;
    }

    public function getUsedRmaQty($itemId, $orderId, $type)
    {
        $totalQty = 0;
        $collection = $this->_detailsCollection->create();
        $tableName = $this->_resource->getTableName('marketplace_rma_items');
        $sql = "main_table.id = rma_items.rma_id ";
        $collection->getSelect()->join(['rma_items' => $tableName], $sql, ['*']);
        $collection->addFilterToMap('item_id', 'rma_items.item_id');
        $condition = "((";
        $condition .= "(rma_items.item_id = $itemId)";
        $condition .= " AND (order_id = $orderId)";
        $condition .= " AND (final_status = 0)";
        $condition .= ")";
        $condition .= " OR ";
        $condition .= " (";
        $condition .= " (rma_items.is_qty_returned = 1)";
        $condition .= " AND (rma_items.item_id = $itemId)";
        $condition .= "))";
        if ($type == 3) {
            $condition .= " AND (resolution_type = 3)";
        } else {
            $condition .= " AND (resolution_type <= 2)";
        }

        $collection->getSelect()->where($condition);
        $collection->getSelect()->group("main_table.id");
        foreach ($collection as $item) {
            $totalQty += $item->getQty();
        }

        return $totalQty;
    }

    public function getAvailableRmaQty($itemId, $orderId, $totalQty, $type)
    {
        $qty = 0;
        $collection = $this->_invoiceItemCollection
                            ->create()
                            ->addFieldToFilter('order_item_id', $itemId);
        foreach ($collection as $item) {
            $qty += $item->getQty();
        }

        $useInvoice = false;
        if ($type == 1) {
            $totalQty = $qty;
        } elseif ($type == 2) {
            $totalQty = $qty;
        } elseif ($type == 3) {
            $totalQty -= $qty;
        }

        $usedQty = $this->getUsedRmaQty($itemId, $orderId, $type);
        $availableQty = $totalQty - $usedQty;
        return $availableQty;
    }

    public function allowGoToAllRma()
    {
        if ($this->_request->getFullActionName() == 'mprmasystem_guest_allrma') {
            return false;
        }

        return true;
    }
    
    public function getIsSeparatePanel()
    {
        $version = (float) $this->_moduleResource->getDbVersion('Webkul_Marketplace');
        if ($version > 2.0) {
            return $this->_mpHelper->getIsSeparatePanel();
        } else {
            return false;
        }
    }
}
