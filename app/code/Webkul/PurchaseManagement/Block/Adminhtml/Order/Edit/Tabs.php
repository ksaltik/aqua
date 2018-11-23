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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Order\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('order_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Order Information'));
    }

    /**
    * Prepare Layout
    *
    * @return $this
    */
    protected function _prepareLayout()
    {
        $this->addTab(
            'form_section',
            [
                'label' => __('Purchase Order'),
                'title' => __('Purchase Order'),
                'content' => $this->getLayout()->createBlock('Webkul\PurchaseManagement\Block\Adminhtml\Quotation\Edit\Tab\View', 'form_section')->setTemplate('Webkul_PurchaseManagement::quotation.phtml')->toHtml(),
            ]
        );
        $this->addTab(
            'picking_grid',
            [
                'label' => __('Incoming Shipment'),
                'url' => $this->getUrl('purchasemanagement/*/picking', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        return parent::_prepareLayout();
    }
}
