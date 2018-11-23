<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_PurchaseManagement
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Quotation;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Massconfirm extends Action
{
    protected $_suppliers;

    protected $_order;

    protected $_orderitem;

    protected $_productloader;

    protected $_storeManager;

    protected $_picking;

    protected $_helper;

    protected $filter;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory,
        \Webkul\PurchaseManagement\Model\PickingFactory  $pickingFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_order=$orderFactory;
        $this->_storeManager=$storeManager;
        $this->_picking=$pickingFactory;
        $this->filter=$filter;
    }

    public function execute()
    {
        try {
            $errorFlag = false;
            $resultRedirect = $this->resultRedirectFactory->create();
            $collection = $this->filter->getCollection($this->_order->create()->getCollection());
            $ids = $collection->getAllIds();
            foreach ($ids as $id) {
                $order_collection = $this->_order->create()->load($id);
                $incerement_id = $order_collection->getIncrementId();
                if ($order_collection->getStatus()==4) {
                    $errorFlag = true;
                    continue;
                }
                $order_collection->setStatus(2);
                $order_collection->setId($id);
                $order_collection->save();
                //Triggering Incoming Shipment/Product Generation
                $this->_picking->create()->triggerPicking($order_collection);
            }
            if ($errorFlag) {
                $this->messageManager->addWarning("Only the non canceled orders can be confirmed.");
            } else {
                $this->messageManager->addSuccess("Quotation has been confirmed successfully.");
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
