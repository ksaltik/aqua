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
namespace Webkul\PurchaseManagement\Block\Adminhtml;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Webkul\Marketplace\Model\Product;

/**
 * Webkul Mpqa Viewall Block
 */
class Email extends \Magento\Framework\View\Element\Template
{
     /**
      * @var \Magento\Customer\Model\Session
      */
    protected $_customerSession;

    protected $_orderitem;

    /**
     * @param \Magento\Framework\View\Element\Template\Context               $context
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Webkul\PurchaseManagement\Model\OrderitemFactory  $orderitemFactory,
        array $data = []
    ) {
        $this->_customerSession= $customerSession;
        $this->_registry = $registry;
        $this->_resource=$resource;
        $this->_orderitem=$orderitemFactory;
        parent::__construct($context, $data);
    }

    public function getItems($purchase_id){
        $items=$this->_orderitem->create()->getCollection()
                    ->addFieldToFilter('purchase_id',$purchase_id);
        if (count($items)) {
            return $items;
        }else return [];
    }
}
