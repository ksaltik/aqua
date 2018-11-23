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
namespace Webkul\PurchaseManagement\Controller\Adminhtml\Suppliers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Getdetails extends \Webkul\PurchaseManagement\Controller\Adminhtml\Suppliers
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    protected $_supplier;

    protected $_options;

    protected $_resultJsonFactory;

    protected $_helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Webkul\PurchaseManagement\Model\SuppliersFactory $supplier,
        \Webkul\PurchaseManagement\Model\SupplieroptionsFactory $options,
        \Webkul\PurchaseManagement\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->_supplier=$supplier;
        $this->_options=$options;
        $this->_helper = $helper;
        $this->_resultJsonFactory=$resultJsonFactory;
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $supplier_id=$this->getRequest()->getParam('supplier_id');
        $data=[];$data1=[];
        if ($supplier_id) {
            $supplier=$this->_supplier->create()->load($supplier_id);
            if (count($supplier)) {
                $data1['name']=$supplier->getName();
                $data1['email']=$supplier->getEmail();
                $data1['company']=$supplier->getCompany();
                $data1['street']=$supplier->getStreet()." ".$supplier->getData('street1');
                $data1['city']=$supplier->getCity();
                $data1['country']= $this->_helper->getCountryname($supplier->getCountry());
                $data1['state']=$supplier->getState();
                $data1['zip']=$supplier->getZip();
                $data1['phone']=$supplier->getPhone();
            }
        }
        array_push($data, $data1);
        $result = $this->_resultJsonFactory->create();
        $result->setData($data);
        return $result;
    }
}
