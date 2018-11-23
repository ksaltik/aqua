<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Block\Adminhtml\Customer\Edit\Tab;

class Commission extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    const COMM_TEMPLATE = 'customer/commission.phtml';

    /**
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Backend\Block\Widget\Context     $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * Set template to itself.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::COMM_TEMPLATE);
        }

        return $this;
    }

    public function getCommission()
    {
        $collection = $this->_objectManager->create(
            'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
        )->getSalesPartnerCollection();
       // $vendorRowCom='';
        if (count($collection)) {
            foreach ($collection as $value) {
                $rowcom = $value->getCommissionRate();
                //$vendorRowCom=$value->getVendorCommissionRate();
            }
        } else {
            $rowcom = $this->_objectManager->create(
                'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
            )->getConfigCommissionRate();
        }
        /*if(empty($vendorRowCom)){
            $vendorRowCom = $this->_objectManager->create(
                'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
            )->getConfigVendorCommissionRate();
        }*/
        $tsale = 0;
        $tcomm = 0;
        $tact = 0;
        $vendorBenefit = 0;
        /*$vtsale = 0;
        $vtcomm = 0;
        $vtact = 0;*/
        $collection1 = $this->_objectManager->create(
            'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
        )->getSalesListCollection();
        foreach ($collection1 as $key) {
            $tsale += $key->getTotalAmount();
            $tcomm += $key->getTotalCommission();
            $tact += $key->getActualSellerAmount();
            $vendorBenefit += $key->getVendorBenefitAmount();
           /* $vtsale += $key->getVendorTotalAmount();
            $vtcomm += $key->getVendorTotalCommission();
            $vtact += $key->getActualAdminAmount();*/
        }

        return [
            'total_sale' => $tsale,
            'total_comm' => $tcomm,
            'actual_seller_amt' => $tact,
            'current_val' => $rowcom,
            'vendor_benefit' => $vendorBenefit,
            /*'vendor_total_sale' => $vtsale,
            'vendor_total_comm' => $vtcomm,
            'actual_admin_amt' => $vtact,

            'vendor_current_val' => $vendorRowCom,*/
        ];
    }

    public function getCurrencySymbol()
    {
        $currencySymbol = $this->_objectManager->create(
            'Webkul\Marketplace\Block\Adminhtml\Customer\Edit'
        )->getCurrencySymbol();

        return $currencySymbol;
    }
}
