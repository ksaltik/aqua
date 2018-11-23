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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Cancel extends Action
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
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Webkul\PurchaseManagement\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_order=$orderFactory;
        $this->_storeManager=$storeManager;
        $this->_picking=$pickingFactory;
        $this->_orderitem=$orderitemFactory;
        $this->_helper=$helper;
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
                    $orderCollection=$this->_order->create()->load($orderId);
                        $orderCollection->setStatus(4)
                                        ->setId($orderId)
                                        ->save();
                    //Triggering Picking and Move Cancel ...
                    $this->_helper->cancelRelatedPicking($orderId);
                }
                
                $this->messageManager->addSuccess(__("Total of %1 record(s) were successfully canceled", count($orderIds)));
            }
            // $this->_redirect("*/purchasemanagement_order/view", array("id" => $id));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        // return $resultRedirect->setPath('*/*/');
    }
}
