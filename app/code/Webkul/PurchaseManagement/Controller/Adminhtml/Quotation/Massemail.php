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

class Massemail extends Action
{
    protected $_suppliers;

    protected $_order;

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
        \Webkul\PurchaseManagement\Helper\Data      $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_order=$orderFactory;
        $this->_storeManager=$storeManager;
        $this->_picking=$pickingFactory;
        $this->_helper=$helper;
        $this->filter=$filter;
    }

    public function execute()
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            $collection = $this->filter->getCollection($this->_order->create()->getCollection());
            $ids = $collection->getAllIds();
            $success_ids='';$failed_ids='';
            foreach ($ids as $id) {
                $order_collection = $this->_order->create()->load($id);
                $status = $order_collection->getStatus();
                if ($status != 4) {
                    $this->_helper->sendRfqEmail($id);
                    $order_collection->setStatus(1);
                    $order_collection->setId($order_collection->getEntityId());
                    $order_collection->save();
                    $success_ids .= $order_collection->getIncrementId().',';
                } else {
                    $failed_ids .= $order->getIncrementId().',';
                }
            }
            $this->messageManager->addSuccess(__("Supplier(s) of Quotation(s) %1 has been Successfully Notified by Email.", rtrim($success_ids, ',')));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        

        return $resultRedirect->setPath('*/*/');
    }
}
