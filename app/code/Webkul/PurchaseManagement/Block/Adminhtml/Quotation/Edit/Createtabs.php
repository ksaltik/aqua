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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Quotation\Edit;

class Createtabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quotation_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Quotation Information'));
    }

    /**
    * Prepare Layout
    *
    * @return $this
    */
    protected function _prepareLayout()
    {
        $block = 'Webkul\PurchaseManagement\Block\Adminhtml\Quotation\Edit\Tab\Suppliergrid';
        $quotation_id = $this->getRequest()->getParam("id");
        if ($quotation_id) {
            $this->addTab(
                'form_section',
                [
                    'label' => __('Quotation Edit'),
                    'title' => __('Quotation Edit'),
                    'content' => $this->getLayout()->createBlock($block, 'form_section')->toHtml(),
                ]
            );
        } else {
            $this->addTab(
                'form_section',
                [
                    'label' => __('Create New Quotation Order'),
                    'title' => __('Create New Quotation Order'),
                    'content' => $this->getLayout()->createBlock($block, 'form_section')->toHtml(),
                ]
            );
        }
        return parent::_prepareLayout();
    }
}
