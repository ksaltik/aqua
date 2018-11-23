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
namespace Webkul\PurchaseManagement\Block\Adminhtml\Move\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('move_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Products Information'));
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
                'label' => __('Incoming Products'),
                'title' => __('Incoming Products'),
                //'content' => $this->getLayout()->createBlock('\Magento\Backend\Block\Template', 'form_section')->setTemplate('Webkul_PurchaseManagement::quotation.phtml')->toHtml(),
            ]
        );
        // $this->getLayout()->addBlock($block, 'supplier44','adminhtml\quotation\create_0.form','supplier44');
        // $this->getLayout()->removeElement('supplier44');
        return parent::_prepareLayout();
    }
}
