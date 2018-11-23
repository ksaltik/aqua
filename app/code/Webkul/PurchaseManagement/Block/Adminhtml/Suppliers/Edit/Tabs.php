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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Suppliers\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('supplier_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Suppliers Information'));
    }

    /**
    * Prepare Layout
    *
    * @return $this
    */
    protected function _prepareLayout()
    {
        $block = 'Webkul\PurchaseManagement\Block\Adminhtml\Suppliers\Edit\Tab\Supplier';
        $this->addTab(
            'supplier',
            [
                'label' => __('Supplier Information'),
                'content' => $this->getLayout()->createBlock($block, 'supplier')->toHtml(),
            ]
        );
        if ($this->getRequest()->getParam('id')) {
            $this->addTab(
                'products',
                [
                    'label' => __('Products'),
                    'url' => $this->getUrl('purchasemanagement/*/products', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
            $this->addTab(
                'quotation',
                [
                    'label' => __('Quotation'),
                    'url' => $this->getUrl('purchasemanagement/*/quotation', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
            $this->addTab(
                'purchase_order',
                [
                    'label' => __('Purchase Order'),
                    'url' => $this->getUrl('purchasemanagement/*/purchaseorder', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
        }
        return parent::_prepareLayout();
    }
}
