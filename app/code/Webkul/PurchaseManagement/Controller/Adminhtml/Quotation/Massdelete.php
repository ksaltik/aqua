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

class Massdelete extends Action
{
    protected $_suppliers;

    protected $_order;

    protected $_orderitem;

    protected $_productloader;

    protected $_storeManager;

    protected $_picking;

    protected $_helper;

    private $filter;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory,
        \Webkul\PurchaseManagement\Model\PickingFactory  $pickingFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderitemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_order=$orderFactory;
        $this->_storeManager=$storeManager;
        $this->_picking=$pickingFactory;
        $this->_orderitem=$orderitemFactory;
        $this->filter=$filter;
    }

    public function execute()
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            $collection = $this->filter->getCollection($this->_order->create()->getCollection());
            $orderIds = $collection->getAllIds();
            
            if (!is_array($orderIds)) {
                $this->messageManager->addError(__("Please select item(s)"));
            } else {
                foreach ($orderIds as $orderId) {
                    $orderCollection=$this->_order->create()->getCollection()
                            ->addFieldToFilter('entity_id', $orderId);
                    foreach ($orderCollection as $order) {
                        $order->setId($order->getEntityId())->delete();
                    }
                    $items = $this->_orderitem->create()->getCollection()->addFieldToFilter("purchase_id", $orderId);
                    foreach ($items as $item) {
                        $item->setId($item->getEntityId())->delete();
                    }
                }
                
                $this->messageManager->addSuccess(__("Total of %1 record(s) were successfully deleted", count($orderIds)));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
