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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Picking;
 
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Receiveproducts extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    protected $_picking;

    protected $_move;

    protected $_order;

    protected $_orderitem;

    protected $_helper;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\PurchaseManagement\Model\PickingFactory  $pickingFactory,
        \Webkul\PurchaseManagement\Model\MoveFactory  $moveFactory,
        \Webkul\PurchaseManagement\Model\OrderFactory  $orderFactory,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderitemFactory,
        \Webkul\PurchaseManagement\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_move=$moveFactory;
        $this->_picking=$pickingFactory;
        $this->_order=$orderFactory;
        $this->_orderitem=$orderitemFactory;
        $this->_helper=$helper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {   
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();

        if ($data) {
            $flag = true;
            $picking_id = $data['id'];

            $picking = $this->_picking->create()->load($picking_id);

            $picking_increment_id = $picking->getIncrementId();
            $purchase_id = $picking->getPurchaseId();
            
            $order = $this->_order->create()->load($purchase_id);
            
            if ($picking_id) {
                // Preparing Move Quantity

                $moves = $this->_move->create()
                        ->getCollection()
                        ->addFieldToFilter("picking_id", $picking_id)
                        ->addFieldToFilter("status", 1);
                foreach ($moves as $move) {
                    $mv = $this->_move->create()->load($move->getEntityId());
                    $product_id = $mv->getProductId();
                    if (isset($data['qty'][$product_id])) {
                        $receive_qty = $data['qty'][$product_id];
                        $quantity = $mv->getQuantity();
                        if ($receive_qty < $quantity) {
                            $mv->setQuantity($receive_qty);
                        } else {
                            $data['qty'][$product_id] = $quantity;
                            $receive_qty = $data['qty'][$product_id];
                        }
                        $mv->setStatus(2);
                        $mv->setId($move->getEntityId());
                        $mv->save();
                        $this->_helper->updateProductStock($product_id,$receive_qty);
                    }
                }
                $picking->setStatus(2);
                $picking->setId($picking_id);
                $picking->save();
            }
            /* Setting received qty inside order items*/
            $items = $this->_orderitem->create()->getCollection()->addFieldToFilter("purchase_id", $purchase_id);
            foreach ($items as $item) {
                $itm = $this->_orderitem->create()->load($item->getEntityId());
                $product_id = $itm->getProductId();
                $quantity = $itm->getQuantity();
                $receive_qty = $itm->getReceivedQty();
                
                if ($receive_qty >= $quantity) {
                    continue;
                }
                if (isset($data['qty'][$product_id])) {
                    $net_received = $receive_qty + $data['qty'][$product_id];
                    $itm->setReceivedQty($net_received);
                    $itm->setId($item->getEntityId());
                    $itm->save();
                    if ($quantity > $net_received) {
                        $flag = false;
                    }
                }
            }
            /* if all picking and moves are received then order status status changed to received */
            if ($flag) {
                $order->setStatus(3);
                $order->setId($purchase_id);
                $order->save();
            } else {
                $this->_picking->create()->triggerPicking($order);
            }
        }
        
        $this->messageManager->addSuccess(__('Shipment %1 Received Successfully.',$picking_increment_id));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_PurchaseManagement::shipments');
    }
}
