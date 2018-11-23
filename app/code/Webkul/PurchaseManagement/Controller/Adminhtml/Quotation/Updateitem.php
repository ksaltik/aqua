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
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Updateitem extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_helper;

    protected $_history;

    private $order;

    private $orderitem;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\HistoryFactory  $historyFactory,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderitemFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_history=$historyFactory;
        $this->_helper=$helper;
        $this->_resultJsonFactory=$resultJsonFactory;
        $this->order=$orderFactory;
        $this->orderitem=$orderitemFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {  
            try{
                $item_id = $this->getRequest()->getParam("item_id");
                $cost_price = $this->getRequest()->getParam("price");
                $cost_price = round($cost_price, 2);
                $quantity = $this->getRequest()->getParam("qty");
                $new_total = $cost_price * $quantity;
                /* Load purchase item order item*/
                $item_collection = $this->orderitem->create()->load($item_id);
                $old_total = $item_collection->getSubtotal();
                $old_quantity = $item_collection->getQuantity();
                if ($old_total != $new_total || $old_quantity != $quantity) {
                    $price_diff = $new_total - $old_total;
                    $quantity_diff = $quantity - $old_quantity;
                    $order_id = $item_collection->getPurchaseId();
                    /* Order Collection load for calculation of grand and item_count total*/
                    $order_collection = $this->order->create()->load($order_id);
                    $old_sub_total = $order_collection->getBaseSubtotal();
                    $old_item_count = $order_collection->getTotalItemCount();
                    $new_sub_total = $old_sub_total + $price_diff;
                    $new_item_count = $old_item_count + $quantity_diff;
                    $order_collection->setGrandTotal($new_sub_total);
                    $order_collection->setBaseSubtotal($new_sub_total);
                    $order_collection->setTotalItemCount($new_item_count);
                    $order_collection->setId($order_id);
                    $order_collection->save();
                    $item_collection->setBasePrice($cost_price);
                    $item_collection->setQuantity($quantity);
                    $item_collection->setSubtotal($new_total);
                    $item_collection->setId($item_id);
                    $item_collection->save();
                }
            }catch(\Exception $e){
                $this->messageManager->addError($e->getMessage());
            }
        }
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_PurchaseManagement::quotation');
    }
}
