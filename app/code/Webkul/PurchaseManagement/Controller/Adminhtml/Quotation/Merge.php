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

class Merge extends Action
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
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderitemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_localeDate = $localeDate;
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
            $source = '';
            $supplier_id = 0;
            $currency_code = '';
            $supplier_email = '';
            $source_type = 'merge';
            $status_error = false;
            $supplier_error = false;

            $collection = $this->filter->getCollection($this->_order->create()->getCollection());
            $ids = $collection->getAllIds();
            if (count($ids) < 2) {
                $this->messageManager->addError("Sorry, Atleast Select 2 Quotation(s) for Merge Operation !!!");
                $this->_redirect("*/*/");
                return;
            }
            foreach ($ids as $id) {
                $collection = $this->_order->create()->load($id);
                if ($collection->getStatus() == 4) {
                    $status_error = true;
                    break;
                }
                if ($supplier_id>0 && $supplier_id != $collection->getSupplierId()) {
                    $supplier_error = true;
                    break;
                }
                $supplier_id = $collection->getSupplierId();
                $supplier_email = $collection->getSupplierEmail();
                $source = $source." ".$collection->getIncrementId();
                $currency_code = $collection->getGlobalCurrencyCode();
            }
            if ($status_error) {
                $this->messageManager->addError("Merge Error, Canceled Quotation(s) Cannot be Merged !!!");
            } elseif ($supplier_error) {
                $this->messageManager->addError("Merge Error, Quotation(s) Must belong to same supplier !!!");
            } else {
                $total_quantity = 0;
                $total_weight = 0.0;
                $total_subtotal = 0.0;
                $quotation_model = $this->_order->create();
                $quotation_model->setSource($source);
                $quotation_model->setSourceType($source_type);
                $quotation_model->setSupplierId($supplier_id);
                $quotation_model->setSupplierEmail($supplier_email);
                $quotation_model->setGlobalCurrencyCode($currency_code);
                $quotation_model->save();
                $quotation_id = $quotation_model->getId();
                $items = $this->_orderitem->create()->getCollection()
                        ->addFieldToFilter("purchase_id", ['in',$ids]);
                $items = $this->mergeItems($items->getData());
                foreach ($items as $item) {
                    $item_model = $this->_orderitem->create();
                    $item_model->setPurchaseId($quotation_id);
                    $item_model->setProductId($item['product_id']);
                    $item_model->setSku($item['sku']);
                    $item_model->setDescription($item['description']);
                    // $item_model->setCustomOptions($item['custom_options']);
                    $item_model->setQuantity($item['quantity']);
                    $item_model->setBasePrice($item['base_price']);
                    $item_model->setWeight($item['weight']);
                    $item_model->setSubtotal($item['subtotal']);
                    $item_model->setCurrency($item['currency']);
                    $item_model->save();
                    $total_weight += $item['weight'];
                    $total_subtotal += $item['subtotal'];
                    $total_quantity += $item['quantity'];
                }
                $increment_id = $this->_helper->getIncrementNumber($quotation_id);

                $quotation_model->setGrandTotal($total_subtotal);
                $quotation_model->setBaseSubtotal($total_subtotal);
                $quotation_model->setWeight($total_weight);
                $quotation_model->setTotalItemCount($total_quantity);
                $quotation_model->setIncrementId($increment_id);
                $quotation_model->setId($quotation_id);
                $time=$this->_localeDate->date()
                                             ->format('h:i:sa');
                $quotation_model->setCreatedAt($time);
                $quotation_model->save();
                foreach ($ids as $id) {
                    $collection = $this->_order->create()->load($id);
                    $collection->setStatus(4)->setId($id);
                    $collection->save();
                    //add code to cancel related picking and moves.
                }
                $this->messageManager->addSuccess(__("Quotation(s) has been Merged Successfully, Merged Quotation is %1",$increment_id));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function mergeItems($items)
    {
        $new_items = [];
        foreach ($items as $item) {
            $flag = true;
            foreach ($new_items as $key => $new) {
                if ($new['product_id'] == $item['product_id']) {
                    $new_items[$key]['quantity'] += $item['quantity'];
                    $new_qty = $new_items[$key]['quantity'];
                    $new_items[$key]['subtotal'] = $new_qty * $item['base_price'];
                    $flag = false;
                }
            }
            if ($flag) {
                array_push($new_items, $item);
            }
        }
        return $new_items;
    }
}
